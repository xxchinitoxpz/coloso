<?php

namespace App\Orchid\Screens;

use App\Models\Payment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PaymentListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'payments' => Payment::orderBy('created_at', 'desc')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Payments';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Payment')
                ->icon('plus')
                ->route('platform.payment.create'),
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
            Layout::table('payments', [
                TD::make('id', 'ID'),
                TD::make('amount', 'Amount'),
                TD::make('type_payment_id', 'Type Payment')->render(function ($payment) {
                    return $payment->typePayment->name;
                }),
                TD::make('sale_id', 'Sale ID'),
                TD::make('created_at', 'Created At'),
                TD::make('updated_at', 'Updated At'),
            ]),
        ];
    }
}
