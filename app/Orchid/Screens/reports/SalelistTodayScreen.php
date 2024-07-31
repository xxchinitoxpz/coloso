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
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('total', 'Total')->align(TD::ALIGN_CENTER),
                TD::make('balance', 'Balance')->align(TD::ALIGN_CENTER),
                TD::make('final_payment_date', 'Final Payment Date')->align(TD::ALIGN_CENTER),
                TD::make('state', 'State')->align(TD::ALIGN_CENTER)
                    ->render(function (Sale $sale) {
                        return $sale->state ? 'Pagado' : 'Por Pagar';
                    }),
                TD::make('customer_id', 'Customer')->align(TD::ALIGN_CENTER)
                    ->render(function (Sale $sale) {
                        return $sale->customer ? $sale->customer->name : 'Unknown';
                    }),
                TD::make('created_at', 'Created At')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('updated_at', 'Updated At')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('details', 'Details')->align(TD::ALIGN_CENTER)
                    ->render(function (Sale $sale) {
                        return Link::make('')
                            ->icon('eye')
                            ->route('platform.sale.details.today', $sale->id);
                    }),
            ]),
        ];
    }
}
