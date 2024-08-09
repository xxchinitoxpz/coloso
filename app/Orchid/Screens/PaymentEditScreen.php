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
use Carbon\Carbon;

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
        return 'Crear/Editar Pago';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.payments',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Guardar')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('payment.amount')
                    ->title('Monto')
                    ->type('number')
                    ->step('0.01') // Permitir decimales
                    ->required(),
                Select::make('payment.type_payment_id')
                    ->title('Tipo de Pago')
                    ->options(TypePayment::pluck('type_payment', 'id')->toArray())
                    ->empty('Seleccionar Tipo de Pago')
                    ->required(),
                Select::make('payment.sale_id')
                    ->title('Venta')
                    ->options(Sale::where('state', false)->with('customer')->get()->mapWithKeys(function ($sale) {
                        return [$sale->id => $sale->customer->name . ' | Deuda: ' . ($sale->total - $sale->balance)];
                    })->toArray())
                    ->empty('Seleccionar Venta')
                    ->required(),
            ]),
        ];
    }

    public function save(Request $request, Payment $payment)
    {
        DB::beginTransaction();
        try {
            // Guardar el pago
            $payment->fill($request->get('payment'))->save();

            // Obtener la venta asociada
            $sale = Sale::find($payment->sale_id);

            // Actualizar el balance de la venta
            $sale->balance += $payment->amount;

            // Verificar si la venta está completamente pagada
            if ($sale->balance >= $sale->total) {
                $sale->state = true;
                $sale->final_payment_date = Carbon::now(); // Actualizar la fecha de pago final
            }

            $sale->save();

            DB::commit();
            Alert::info('Has creado/actualizado el pago exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Ocurrió un error al guardar el pago: ' . $e->getMessage());
        }

        return redirect()->route('platform.payment.list');
    }
}
