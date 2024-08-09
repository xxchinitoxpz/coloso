<?php

namespace App\Orchid\Screens;

use App\Models\Purchases;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PurchaseDetailScreen extends Screen
{
    public $purchase;

    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(Purchases $purchase): iterable
    {
        return [
            'purchase' => $purchase->load('products'), // Cargar los productos asociados con la compra
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Detalles de Compra';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.purchases',
        ];
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
                ['name' => 'Compras', 'route' => route('platform.purchase.list')],
                ['name' => 'Detalles de Compra'],
            ]]),
            Layout::table('purchase.products', [ // Usar 'purchase.products' para acceder a la relación
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
