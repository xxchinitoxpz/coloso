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
        return 'Crear/Editar Alquiler';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Guardar')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.rentals',
        ];
    }

    public function layout(): array
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Inicio', 'route' => route('platform.main')],
                ['name' => 'Alquileres', 'route' => route('platform.rental.list')],
                ['name' => $this->rental->exists ? 'Editar Alquiler' : 'Crear Alquiler'],
            ]]),
            Layout::rows([
                Relation::make('rental.customer_id')
                    ->title('Cliente')
                    ->fromModel(Customer::class, 'name')
                    ->required(),
                Select::make('rental.court_id')
                    ->title('Cancha')
                    ->options(
                        Court::where('state', true)->with('type_court')->get()->mapWithKeys(function ($court) {
                            return [$court->id => $court->formatted_court];
                        })->toArray()
                    )
                    ->empty('Seleccionar cancha')
                    ->required(),
                Input::make('rental.start_time')
                    ->title('Hora de Inicio')
                    ->type('time')
                    ->required(),
                Input::make('rental.total_hours')
                    ->title('Horas Totales')
                    ->type('number')
                    ->required(),
                Input::make('rental.end_time')
                    ->title('Hora de Fin')
                    ->type('time')
                    ->readonly()
                    ->help('La hora de fin se calculará automáticamente.'),
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
            // Guardar el alquiler
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

            // Actualizar el estado de la cancha
            $court = Court::find($courtId);
            if ($court) {
                $court->state = false;
                $court->save();
            }

            DB::commit();

            Alert::info('Has creado/actualizado un alquiler exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Ocurrió un error al guardar el alquiler: ' . $e->getMessage());
        }

        return redirect()->route('platform.rental.list');
    }

    private function calculateTotal($courtId, $startTime, $totalHours)
    {
        // Convertir la hora de inicio a horas
        $startHour = (int) date('H', strtotime($startTime));
        $endHour = ($startHour + $totalHours) % 24;

        // Obtener la tarifa de la cancha
        $tariffs = Tariff::where('court_id', $courtId)->get();

        $total = 0;
        $currentHour = $startHour;

        while ($totalHours > 0) {
            $tariff = $this->getTariffForHour($tariffs, $currentHour);

            // Determinar la próxima hora de transición basada en la hora actual
            $nextTransitionHour = $this->getNextTransitionHour($currentHour);

            // Calcular las horas en el período de tarifa actual
            $hoursInPeriod = min($totalHours, $nextTransitionHour - $currentHour);
            $total += $hoursInPeriod * $tariff->price;

            $totalHours -= $hoursInPeriod;
            $currentHour = $nextTransitionHour;

            // Si estamos después de la medianoche, restablecer la hora actual
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
            return 12; // Transición a la tarde
        } elseif ($currentHour >= 12 && $currentHour < 18) {
            return 18; // Transición a la noche
        } else {
            return 24; // Transición al día siguiente
        }
    }
}
