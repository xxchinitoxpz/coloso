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
     * Obtener los datos que se mostrarán en la pantalla.
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
     * El nombre de la pantalla que se muestra en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Pagos';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.payments',
        ];
    }

    /**
     * Los botones de acción de la pantalla.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Crear Pago')
                ->icon('plus')
                ->route('platform.payment.create'),
        ];
    }

    /**
     * Los elementos de diseño de la pantalla.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('payments', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('amount', 'Monto')->align(TD::ALIGN_CENTER),
                TD::make('type_payment_id', 'Tipo de Pago')->render(function ($payment) {
                    return $payment->typePayment->type_payment;
                })->align(TD::ALIGN_CENTER),
                TD::make('sale_id', 'ID de Venta')->align(TD::ALIGN_CENTER),
                TD::make('created_at', 'Creado En')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('updated_at', 'Actualizado En')->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),
            ]),
        ];
    }

    /**
     * Obtener el número de modelos a devolver por página
     *
     * @return int
     */
    public static function perPage(): int
    {
        return 10;
    }
}
