<?php

namespace App\Orchid\Screens;

use App\Models\Rental;
use App\Models\Reservation;
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
    public function query(): iterable
    {
        return [
            'rentals' => Rental::orderBy('created_at', 'desc')->paginate(),
            'reservations' => Reservation::orderBy('created_at', 'desc')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Rentals and Reservations';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.rentals',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Create Rental')
                ->icon('plus')
                ->route('platform.rental.create'),
            Link::make('Create Reservation')
                ->icon('plus')
                ->route('platform.reservation.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Home', 'route' => route('platform.main')],
                ['name' => 'Rentals and Reservations', 'route' => route('platform.rental.list')],
            ]]),
            Layout::table('rentals', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('total', 'Total')->align(TD::ALIGN_CENTER),
                TD::make('start_time', 'Start Time')->align(TD::ALIGN_CENTER),
                TD::make('end_time', 'End Time')->align(TD::ALIGN_CENTER),
                TD::make('state', 'State')->render(function ($rental) {
                    return $rental->state ? 'Completed' : 'Pending';
                })->align(TD::ALIGN_CENTER),
                TD::make('customer_id', 'Customer')->render(function ($rental) {
                    return $rental->customer->name;
                })->align(TD::ALIGN_CENTER),
                TD::make('type_court', 'Type of Court')
                    ->render(function ($rental) {
                        return $rental->court && $rental->court->type_court ? $rental->court->type_court->type_court : 'N/A';
                    })->align(TD::ALIGN_CENTER),
                TD::make('created_at', 'Created At')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('action', 'Action')->render(function ($rental) {
                    return Button::make('Culminate')
                        ->icon('check')
                        ->method('culminateRental')
                        ->parameters(['rental_id' => $rental->id]);
                })->align(TD::ALIGN_CENTER),
            ])->title('Rentals'),

            Layout::table('reservations', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('total', 'Total')->align(TD::ALIGN_CENTER),
                TD::make('start_time', 'Start Time')->align(TD::ALIGN_CENTER),
                TD::make('end_time', 'End Time')->align(TD::ALIGN_CENTER),
                TD::make('state', 'State')->render(function ($reservation) {
                    return $reservation->state ? 'Completed' : 'Pending';
                })->align(TD::ALIGN_CENTER),
                TD::make('customer_id', 'Customer')->render(function ($reservation) {
                    return $reservation->customer->name;
                })->align(TD::ALIGN_CENTER),
                TD::make('type_court', 'Type of Court')
                    ->render(function ($reservation) {
                        return $reservation->court && $reservation->court->type_court ? $reservation->court->type_court->type_court : 'N/A';
                    })->align(TD::ALIGN_CENTER),
                TD::make('created_at', 'Created At')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('action', 'Action')->render(function ($reservation) {
                    return
                        Button::make('Edit')
                        ->icon('pencil')
                        ->method('editReservation')
                        ->parameters(['reservation_id' => $reservation->id]) .
                        Button::make('Culminate')
                        ->icon('check')
                        ->method('culminateReservation')
                        ->parameters(['reservation_id' => $reservation->id]);
                })->align(TD::ALIGN_CENTER),
            ])->title('Reservations'),
        ];
    }

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

    public function culminateReservation(Request $request)
    {
        $reservationId = $request->input('reservation_id');

        DB::beginTransaction();
        try {
            $reservation = Reservation::find($reservationId);
            if ($reservation) {
                $reservation->state = true;
                $reservation->save();

                $court = $reservation->court;
                if ($court) {
                    $court->state = true;
                    $court->save();
                }
            }

            DB::commit();

            Alert::info('Reservation and court state have been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('An error occurred while updating the reservation and court: ' . $e->getMessage());
        }

        return redirect()->route('platform.rental.list');
    }

    public function editReservation(Request $request)
    {
        $reservationId = $request->input('reservation_id');
        return redirect()->route('platform.reservation.edit', ['reservation' => $reservationId]);
    }
}
