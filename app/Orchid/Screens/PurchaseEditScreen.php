<?php

namespace App\Orchid\Screens;

use App\Models\Products;
use App\Models\Purchases;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;

class PurchaseEditScreen extends Screen
{
    public $venta;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Purchases $venta): iterable
    {
        return [
            'venta' => $venta,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'PurchaseEditScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Crear Venta')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->venta->exists),

            Button::make('Actualizar Venta')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->venta->exists),

            Button::make('Eliminar')
                ->icon('trash')
                ->confirm('¿Estás seguro de que deseas eliminar esta venta?')
                ->method('remove')
                ->canSee($this->venta->exists),
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

            DateTimer::make('venta.fecha')
                ->title('Fecha')
                ->required(),

            Input::make('venta.total')
                ->title('Total')
                ->type('number')
                ->required(),

            Repeater::make('productos')
                ->title('Productos')
                ->layout([
                    Relation::make('productos.[]')
                        ->fromModel(Products::class, 'nombre')
                        ->title('Producto')
                        ->required(),
                    Input::make('cantidad')
                        ->title('Cantidad')
                        ->type('number')
                        ->required(),
                    Input::make('precio')
                        ->title('Precio')
                        ->type('number')
                        ->required(),

                ]),

        ];
    }
}
