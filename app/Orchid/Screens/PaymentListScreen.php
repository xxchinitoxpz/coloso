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

    public function permission(): ?iterable
    {
        return [
            'platform.systems.payments',
        ];
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
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('amount', 'Amount')->align(TD::ALIGN_CENTER),
                TD::make('type_payment_id', 'Type Payment')->render(function ($payment) {
                    return $payment->typePayment->type_payment;
                })->align(TD::ALIGN_CENTER),
                TD::make('sale_id', 'Sale ID')->align(TD::ALIGN_CENTER),
                TD::make('created_at', 'Created At')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('updated_at', 'Updated At')->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),
            ]),
        ];
    }

    /**
     * Get the number of models to return per page
     *
     * @return int
     */
    public static function perPage(): int
    {
        return 10;
    }
}
