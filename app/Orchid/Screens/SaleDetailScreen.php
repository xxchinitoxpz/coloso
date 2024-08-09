<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class SaleDetailScreen extends Screen
{
    public $sale;

    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(Sale $sale): iterable
    {
        return [
            'sale' => $sale->load('products', 'customer'),
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Detalles de la Venta';
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

    public function permission(): ?iterable
    {
        return [
            'platform.systems.sales',
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
                ['name' => 'Detalles de la Venta'],
            ]]),

            Layout::table('sale.products', [
                TD::make('name', 'Nombre del Producto')
                    ->render(function ($product) {
                        return $product->name;
                    }),
                TD::make('pivot.quantity', 'Cantidad'),
                TD::make('pivot.subtotal', 'Subtotal'),
            ]),
        ];
    }
}
