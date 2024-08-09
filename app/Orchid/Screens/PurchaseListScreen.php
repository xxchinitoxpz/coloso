<?php

namespace App\Orchid\Screens;

use App\Models\Purchases;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;

class PurchaseListScreen extends Screen
{
    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'purchases' => Purchases::orderBy('created_at', 'desc')->paginate(),
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Lista de Compras';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.purchases',
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
            Link::make('Crear Compra')
                ->icon('plus')
                ->route('platform.purchase.create'),
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
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Inicio', 'route' => route('platform.main')],
                ['name' => 'Compras', 'route' => route('platform.purchase.list')],
            ]]),
            Layout::table('purchases', [
                TD::make('id', 'ID')->align(TD::ALIGN_CENTER),
                TD::make('total', 'Total')->align(TD::ALIGN_CENTER),
                TD::make('created_at', 'Creado En')
                    ->render(function ($model) {
                        return $model->created_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('updated_at', 'Actualizado En')
                    ->render(function ($model) {
                        return $model->updated_at->toDateTimeString();
                    })->align(TD::ALIGN_CENTER),
                TD::make('details', 'Detalles')
                    ->render(function (Purchases $purchase) {
                        return Link::make('')
                            ->icon('eye')
                            ->route('platform.purchase.details', $purchase->id);
                    }),
            ]),
        ];
    }
}
