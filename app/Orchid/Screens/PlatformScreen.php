<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Facades\DB;

class PlatformScreen extends Screen
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

        // Calculate total debts (sales pending to be paid)
        $salesPending = Sale::where('state', false)
            ->sum(DB::raw('total - balance')) ?? 0;

        // Calculate the number of customers
        $customersCount = Customer::count() ?? 0;

        // Calculate actual daily profits (sum of payments made today)
        $paymentsToday = Payment::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('amount') ?? 0;

        return [
            'metrics' => [
                'salesp'   => ['value' => number_format((float)$salesPending, 2)],
                'profit'   => ['value' => number_format((float)$paymentsToday, 2)],
                'customer' => ['value' => number_format((float)$customersCount)],
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Home';
    }

    /**
     * Display header description.
     */
    // public function description(): ?string
    // {
    //     return 'Welcome to your application.';
    // }

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
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Pagos del día' => 'metrics.profit',
                'Deudas totales' => 'metrics.salesp',    
                'Clientes' => 'metrics.customer',
            ]),
            Layout::view('welcome_image'), 
        ];
    }
}
