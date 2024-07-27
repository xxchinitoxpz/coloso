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
     * Fetch data to be displayed on the screen.
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
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Purchase Details';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
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
                ['name' => 'Purchases', 'route' => route('platform.purchase.list')],
                ['name' => 'Purchase Details'],
            ]]),
            Layout::table('purchase.products', [ // Usar 'purchase.products' para acceder a la relación
                TD::make('name', 'Product Name')
                    ->render(function ($product) {
                        return $product->name;
                    }),
                TD::make('pivot.quantity', 'Quantity'),
                TD::make('pivot.subtotal', 'Subtotal'),
            ]),
        ];
    }
}
