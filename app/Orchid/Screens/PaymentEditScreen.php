<?php

namespace App\Orchid\Screens;

use App\Models\Payment;
use App\Models\TypePayment;
use App\Models\Sale;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentEditScreen extends Screen
{
    public $payment;

    public function query(Payment $payment): iterable
    {
        return [
            'payment' => $payment,
        ];
    }

    public function name(): ?string
    {
        return 'Create/Edit Payment';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('payment.amount')
                    ->title('Amount')
                    ->type('number')
                    ->required(),
                Select::make('payment.type_payment_id')
                    ->title('Type Payment')
                    ->options(TypePayment::pluck('type_payment', 'id')->toArray())
                    ->empty('Select Type Payment')
                    ->required(),
                Select::make('payment.sale_id')
                    ->title('Sale')
                    ->options(Sale::where('state', false)->with('customer')->get()->mapWithKeys(function ($sale) {
                        return [$sale->id => $sale->customer_debt];
                    })->toArray())
                    ->empty('Select Sale')
                    ->required(),
            ]),
        ];
    }

    public function save(Request $request, Payment $payment)
    {
        DB::beginTransaction();
        try {
            $payment->fill($request->get('payment'))->save();
            DB::commit();
            Alert::info('You have successfully created/updated a payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('An error occurred while saving the payment: ' . $e->getMessage());
        }

        return redirect()->route('platform.payment.list');
    }
}
