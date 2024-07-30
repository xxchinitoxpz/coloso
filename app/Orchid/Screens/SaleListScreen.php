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
     * Fetch data to be displayed on the screen.
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
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'SaleListScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Sale')
                ->icon('plus')
                ->route('platform.sale.create'),

        ];
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
            ]]),
            Layout::table('sales', [
                TD::make('id', 'ID'),
                TD::make('total', 'Total'),
                TD::make('balance', 'Balance'),
                TD::make('final_payment_date', 'Final Payment Date')
                    ->render(function (Sale $sale) {
                        return $sale->final_payment_date
                            ? $sale->final_payment_date->format('Y-m-d')
                            : 'En Proceso';
                    }),
                TD::make('state', 'State')
                    ->render(function (Sale $sale) {
                        return $sale->state ? 'Pagado' : 'Por Pagar';
                    }),
                TD::make('customer_id', 'Customer')
                    ->render(function (Sale $sale) {
                        return $sale->customer ? $sale->customer->name : 'Unknown';
                    }),
                TD::make('created_at', 'Created At'),
                TD::make('updated_at', 'Updated At'),
                TD::make('details', 'Details')
                    ->render(function (Sale $sale) {
                        return Link::make('')
                            ->icon('eye')
                            ->route('platform.sale.details', $sale->id);
                    }),    
            ]),
        ];
    }
}
