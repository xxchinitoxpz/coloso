<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use App\Models\Rental;
use App\Models\Court;
use App\Models\Tariff;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Select;

class RentalEditScreen extends Screen
{
    public $rental;

    public function query(Rental $rental): iterable
    {
        return [
            'rental' => $rental->load('customer', 'court'),
        ];
    }

    public function name(): ?string
    {
        return 'Create/Edit Rental';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Home', 'route' => route('platform.main')],
                ['name' => 'Rentals', 'route' => route('platform.rental.list')],
                ['name' => $this->rental->exists ? 'Edit Rental' : 'Create Rental'],
            ]]),
            Layout::rows([
                Relation::make('rental.customer_id')
                    ->title('Customer')
                    ->fromModel(Customer::class, 'name')
                    ->required(),
                Select::make('rental.court_id')
                    ->title('Court')
                    ->options(
                        Court::where('state', true)->with('type_court')->get()->mapWithKeys(function ($court) {
                            return [$court->id => $court->formatted_court];
                        })->toArray()
                    )
                    ->empty('Select court')
                    ->required(),
                Input::make('rental.start_time')
                    ->title('Start Time')
                    ->type('time')
                    ->required(),
                Input::make('rental.total_hours')
                    ->title('Total Hours')
                    ->type('number')
                    ->required(),
                Input::make('rental.end_time')
                    ->title('End Time')
                    ->type('time')
                    ->readonly()
                    ->help('End time will be calculated automatically.'),
            ]),
        ];
    }

    public function save(Request $request, Rental $rental)
    {
        $userId = Auth::id();
        $customerId = $request->input('rental.customer_id');
        $courtId = $request->input('rental.court_id');
        $startTime = $request->input('rental.start_time');
        $totalHours = $request->input('rental.total_hours');
        $endTime = date('H:i', strtotime($startTime) + ($totalHours * 3600));

        DB::beginTransaction();
        try {
            // Save the rental
            $rental->fill([
                'total' => $this->calculateTotal($courtId, $startTime, $totalHours),
                'state' => false,
                'start_time' => $startTime,
                'total_hours' => $totalHours,
                'end_time' => $endTime,
                'user_id' => $userId,
                'customer_id' => $customerId,
                'court_id' => $courtId,
            ])->save();

            // Update the court state
            $court = Court::find($courtId);
            if ($court) {
                $court->state = false;
                $court->save();
            }

            DB::commit();

            Alert::info('You have successfully created/updated a rental.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('An error occurred while saving the rental: ' . $e->getMessage());
        }

        return redirect()->route('platform.rental.list');
    }

    private function calculateTotal($courtId, $startTime, $totalHours)
    {
        // Convert start time to hours
        $startHour = (int) date('H', strtotime($startTime));
        $endHour = ($startHour + $totalHours) % 24;

        // Get the court's tariff
        $tariffs = Tariff::where('court_id', $courtId)->get();

        $total = 0;
        $currentHour = $startHour;

        while ($totalHours > 0) {
            $tariff = $this->getTariffForHour($tariffs, $currentHour);

            // Determine the next transition time based on current hour
            $nextTransitionHour = $this->getNextTransitionHour($currentHour);

            // Calculate hours in the current tariff period
            $hoursInPeriod = min($totalHours, $nextTransitionHour - $currentHour);
            $total += $hoursInPeriod * $tariff->price;

            $totalHours -= $hoursInPeriod;
            $currentHour = $nextTransitionHour;

            // If we are past midnight, reset current hour
            if ($currentHour >= 24) {
                $currentHour = 0;
            }
        }

        return $total;
    }

    private function getTariffForHour($tariffs, $hour)
    {
        if ($hour >= 6 && $hour < 12) {
            return $tariffs->firstWhere('schedule', 'D');
        } elseif ($hour >= 12 && $hour < 18) {
            return $tariffs->firstWhere('schedule', 'T');
        } else {
            return $tariffs->firstWhere('schedule', 'N');
        }
    }

    private function getNextTransitionHour($currentHour)
    {
        if ($currentHour >= 6 && $currentHour < 12) {
            return 12; // Transition to afternoon
        } elseif ($currentHour >= 12 && $currentHour < 18) {
            return 18; // Transition to evening
        } else {
            return 24; // Transition to next day
        }
    }
}
