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
     * Fetch data to be displayed on the screen.
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
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'SaleDetailScreen';
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
                ['name' => 'Sales', 'route' => route('platform.sale.list')],
                ['name' => 'Sale Details'],
            ]]),

            // TD::make('sale.customer_id', 'Customer Name')
            // ->render(function ($sale) {
            //     return $sale->customer->name;
            // }),
            
            Layout::table('sale.products', [
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
