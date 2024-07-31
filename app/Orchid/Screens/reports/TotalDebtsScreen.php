<?php

namespace App\Orchid\Screens\reports;

use App\Models\Customer;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class TotalDebtsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Fetch customers with their total debts
        $customers = Customer::with(['sales' => function ($query) {
            $query->where('state', false);
        }])->get();

        // Calculate total debt for each customer
        $customers->each(function ($customer) {
            $customer->total_debt = $customer->sales->sum(function ($sale) {
                return $sale->total - $sale->balance;
            });
        });

        return [
            'customers' => $customers->filter(function ($customer) {
                return $customer->total_debt > 0;
            }),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Total Debts';
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
                ['name' => 'Total Debts'],
            ]]),
            Layout::table('customers', [
                TD::make('id', 'ID'),
                TD::make('name', 'Name'),
                TD::make('total_debt', 'Total Debt')
                    ->render(function ($customer) {
                        return number_format($customer->total_debt, 2);
                    }),
            ]),
        ];
    }
}
