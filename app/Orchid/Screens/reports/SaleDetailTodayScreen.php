<?php

namespace App\Orchid\Screens\reports;

use App\Models\Sale;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class SaleDetailTodayScreen extends Screen
{
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
        return 'Sale Detail';
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
                ['name' => 'Sales of the Day', 'route' => route('platform.sales.list.today')],
                ['name' => 'Sale Detail'],
            ]]),
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
