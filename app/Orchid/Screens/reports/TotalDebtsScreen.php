<?php

namespace App\Orchid\Screens\reports;

use App\Models\Customer;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class TotalDebtsScreen extends Screen
{
    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Obtener clientes con sus deudas totales
        $customers = Customer::with(['sales' => function ($query) {
            $query->where('state', false);
        }])->get();

        // Calcular la deuda total para cada cliente
        $customers->each(function ($customer) {
            $customer->total_debt = $customer->sales->sum(function ($sale) {
                return $sale->total - $sale->balance;
            });
        });

        return [
            'customers' => $customers->filter(function ($customer) {
                return $customer->total_debt > 0;
            }),
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Deudas Totales';
    }

    /**
     * Los botones de acción de la pantalla.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Los elementos de diseño de la pantalla.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Inicio', 'route' => route('platform.main')],
                ['name' => 'Deudas Totales'],
            ]]),
            Layout::table('customers', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('name', 'Nombre')->align(TD::ALIGN_CENTER),
                TD::make('total_debt', 'Deuda Total')->align(TD::ALIGN_CENTER)
                    ->render(function ($customer) {
                        return number_format($customer->total_debt, 2);
                    }),
            ]),
        ];
    }
}
