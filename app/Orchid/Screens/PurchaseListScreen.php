<?php

namespace App\Orchid\Screens;

use App\Models\Purchases;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;

class PurchaseListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'ventas' => Purchases::paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'PurchaseListScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Crear Venta')
                ->icon('plus')
                ->route('platform.venta.edit')
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
            Layout::columns('ventas', [
                TD::make('id', 'ID')
                    ->sort(),
                TD::make('fecha', 'Fecha')
                    ->sort(),
                TD::make('total', 'Total')
                    ->sort(),
                TD::make('created_at', 'Creado')
                    ->sort(),
                TD::make('updated_at', 'Actualizado')
                    ->sort(),
                TD::make('Acciones')
                    ->render(function (Venta $venta) {
                        return Link::make('Editar')
                            ->route('platform.venta.edit', $venta);
                    }),
            ]),
        ];
    }
}
