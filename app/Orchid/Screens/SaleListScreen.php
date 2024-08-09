<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class SaleListScreen extends Screen
{
    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'sales' => Sale::orderBy('created_at', 'desc')->paginate(),
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Lista de Ventas';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.sales',
        ];
    }

    /**
     * Los botones de acción de la pantalla.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Crear Venta')
                ->icon('plus')
                ->route('platform.sale.create'),
        ];
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
                ['name' => 'Ventas', 'route' => route('platform.sale.list')],
            ]]),
            Layout::table('sales', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('total', 'Total')->align(TD::ALIGN_CENTER),
                TD::make('balance', 'Balance')->align(TD::ALIGN_CENTER),
                TD::make('final_payment_date', 'Fecha de Pago Final')->align(TD::ALIGN_CENTER),
                TD::make('state', 'Estado')
                    ->render(function (Sale $sale) {
                        return $sale->state ? 'Pagado' : 'Por Pagar';
                    })->align(TD::ALIGN_CENTER),
                TD::make('customer_id', 'Cliente')
                    ->render(function (Sale $sale) {
                        return $sale->customer ? $sale->customer->name : 'Desconocido';
                    })->align(TD::ALIGN_CENTER),
                TD::make('created_at', 'Fecha de Creación')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                // TD::make('updated_at', 'Actualizado En'),
                TD::make('details', 'Detalles')
                    ->render(function (Sale $sale) {
                        return Link::make('')
                            ->icon('eye')
                            ->route('platform.sale.details', $sale->id);
                    })->align(TD::ALIGN_CENTER),
            ]),
        ];
    }
}
