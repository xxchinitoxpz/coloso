<?php

namespace App\Orchid\Screens;

use App\Models\Rental;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Facades\Alert;

class RentalListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'rentals' => Rental::orderBy('created_at', 'desc')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Rentals';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.rentals',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Rental')
                ->icon('plus')
                ->route('platform.rental.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Home', 'route' => route('platform.main')],
                ['name' => 'Rentals', 'route' => route('platform.rental.list')],
            ]]),
            Layout::table('rentals', [
                TD::make('id', 'ID'),
                TD::make('total', 'Total'),
                TD::make('start_time', 'Start Time'),
                TD::make('end_time', 'End Time'),
                TD::make('state', 'State')->render(function ($rental) {
                    return $rental->state ? 'Completed' : 'Pending';
                }),
                TD::make('customer_id', 'Customer')->render(function ($rental) {
                    return $rental->customer->name;
                }),
                TD::make('type_court', 'Type of Court')
                    ->render(function ($rental) {
                        return $rental->court && $rental->court->type_court ? $rental->court->type_court->type_court : 'N/A';
                    }),
                TD::make('created_at', 'Created At'),
                TD::make('action', 'Action')->render(function ($rental) {
                    return Button::make('Culminate')
                        ->icon('check')
                        ->method('culminateRental')
                        ->parameters(['rental_id' => $rental->id]);
                }),
            ]),
        ];
    }

    /**
     * Culminate a rental and update the court state.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function culminateRental(Request $request)
    {
        $rentalId = $request->input('rental_id');

        DB::beginTransaction();
        try {
            $rental = Rental::find($rentalId);
            if ($rental) {
                $rental->state = true;
                $rental->save();

                $court = $rental->court;
                if ($court) {
                    $court->state = true;
                    $court->save();
                }
            }

            DB::commit();

            Alert::info('Rental and court state have been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('An error occurred while updating the rental and court: ' . $e->getMessage());
        }

        return redirect()->route('platform.rental.list');
    }
}
