<?php

namespace App\Orchid\Screens\reports;

use App\Models\Sale;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class SalelistTodayScreen extends Screen
{
    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Obtener el inicio y el fin del día de hoy
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Obtener las ventas del día de hoy
        return [
            'sales' => Sale::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->orderBy('created_at', 'desc')
                ->paginate(),
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Ventas del Día';
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
                ['name' => 'Ventas del Día'],
            ]]),
            Layout::table('sales', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('total', 'Total')->align(TD::ALIGN_CENTER),
                TD::make('balance', 'Saldo')->align(TD::ALIGN_CENTER),
                TD::make('final_payment_date', 'Fecha de Pago Final')->align(TD::ALIGN_CENTER),
                TD::make('state', 'Estado')->align(TD::ALIGN_CENTER)
                    ->render(function (Sale $sale) {
                        return $sale->state ? 'Pagado' : 'Por Pagar';
                    }),
                TD::make('customer_id', 'Cliente')->align(TD::ALIGN_CENTER)
                    ->render(function (Sale $sale) {
                        return $sale->customer ? $sale->customer->name : 'Desconocido';
                    }),
                TD::make('created_at', 'Creado En')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('updated_at', 'Actualizado En')
                    ->render(function ($model) {
                        return $model->updated_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('details', 'Detalles')->align(TD::ALIGN_CENTER)
                    ->render(function (Sale $sale) {
                        return Link::make('')
                            ->icon('eye')
                            ->route('platform.sale.details.today', $sale->id);
                    }),
            ]),
        ];
    }
}
