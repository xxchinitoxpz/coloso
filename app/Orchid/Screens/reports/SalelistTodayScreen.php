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
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Get the start and end of today
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Fetch sales for today
        return [
            'sales' => Sale::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->orderBy('created_at', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Sales of the Day';
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
                ['name' => 'Sales of the Day'],
            ]]),
            Layout::table('sales', [
                TD::make('id', 'ID'),
                TD::make('total', 'Total'),
                TD::make('balance', 'Balance'),
                TD::make('final_payment_date', 'Final Payment Date'),
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
                            ->route('platform.sale.details.today', $sale->id);
                    }),
            ]),
        ];
    }
}
