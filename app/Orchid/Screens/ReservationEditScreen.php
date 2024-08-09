<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use App\Models\Reservation;
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

class ReservationEditScreen extends Screen
{
    public $reservation;

    public function query(Reservation $reservation): iterable
    {
        return [
            'reservation' => $reservation->load('customer', 'court'),
        ];
    }

    public function name(): ?string
    {
        return 'Crear/Editar Reserva';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Guardar')
                ->icon('check')
                ->method('save'),
        ];
    }


    public function layout(): array
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Inicio', 'route' => route('platform.main')],
                ['name' => 'Alquileres y Reservas', 'route' => route('platform.rental.list')],
                ['name' => $this->reservation->exists ? 'Editar Reserva' : 'Crear Reserva'],
            ]]),
            Layout::rows([
                Relation::make('reservation.customer_id')
                    ->title('Cliente')
                    ->fromModel(Customer::class, 'name')
                    ->required(),
                Select::make('reservation.court_id')
                    ->title('Cancha')
                    ->options(
                        Court::with('type_court')->get()->mapWithKeys(function ($court) {
                            return [$court->id => $court->formatted_court];
                        })->toArray()
                    )
                    ->empty('Seleccionar cancha')
                    ->required(),
                Input::make('reservation.start_time')
                    ->title('Hora de Inicio')
                    ->type('time')
                    ->required(),
                Input::make('reservation.total_hours')
                    ->title('Horas Totales')
                    ->type('number')
                    ->required(),
                Input::make('reservation.end_time')
                    ->title('Hora de Fin')
                    ->type('time')
                    ->readonly()
                    ->help('La hora de fin se calculará automáticamente.'),
            ]),
        ];
    }

    public function save(Request $request, Reservation $reservation)
    {
        $userId = Auth::id();
        $customerId = $request->input('reservation.customer_id');
        $courtId = $request->input('reservation.court_id');
        $startTime = $request->input('reservation.start_time');
        $totalHours = $request->input('reservation.total_hours');
        $endTime = date('H:i', strtotime($startTime) + ($totalHours * 3600));

        DB::beginTransaction();
        try {
            // Guardar la reserva
            $reservation->fill([
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

            Alert::info('Has creado/actualizado una reserva exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Ocurrió un error al guardar la reserva: ' . $e->getMessage());
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

            // Calcular horas en el período tarifario actual
            $hoursInPeriod = min($totalHours, $nextTransitionHour - $currentHour);
            $total += $hoursInPeriod * $tariff->price;

            $totalHours -= $hoursInPeriod;
            $currentHour = $nextTransitionHour;

            // Si pasamos de medianoche, restablecer la hora actual
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
            return 24; // Transición al siguiente día
        }
    }
}
