<?php

namespace App\Orchid\Screens;

use App\Models\Products;
use App\Models\Purchases;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Label;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Layouts\Modal;

class PurchaseEditScreen extends Screen
{
    public $purchase;

    /**
     * Obtener los datos a mostrar en la pantalla.
     *
     * @return array
     */
    public function query(Purchases $purchase): iterable
    {
        return [
            'purchase' => $purchase->load('products'),
        ];
    }

    /**
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Crear/Editar Compra';
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
            Button::make('Guardar')
                ->icon('check')
                ->method('save'),
            // ModalToggle::make('Resumen')
            //     ->icon('eye')
            //     ->modal('showSummary'),
        ];
    }

    /**
     * Los elementos de diseño de la pantalla.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        $products = old('products', [null]);
        $quantities = old('quantities', [1]);

        $productFields = [];
        foreach ($products as $index => $product) {
            $productFields[] = Layout::rows([
                Relation::make("products[$index]")
                    ->title('Producto')
                    ->fromModel(Products::class, 'name')
                    ->required(),
                Input::make("quantities[$index]")
                    ->title('Cantidad')
                    ->type('number')
                    ->required()
                    ->step(1)
                    ->value($quantities[$index]),
            ]);
        }

        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Inicio', 'route' => route('platform.main')],
                ['name' => 'Compras', 'route' => route('platform.purchase.list')],
                ['name' => $this->purchase->exists ? 'Editar Compra' : 'Crear Compra'],
            ]]),
            ...$productFields,
            Layout::rows([
                Button::make('Agregar Otro Producto')
                    ->icon('plus')
                    ->method('addProduct'),
            ]),
            Layout::modal(
                'showSummary',
                Layout::rows([
                    // Aquí puedes agregar el contenido del modal si es necesario
                ])
            )
                ->withoutApplyButton()
                ->withoutCloseButton()
                ->size(Modal::SIZE_XL)
                ->type(Modal::TYPE_RIGHT),
        ];
    }

    public function save(Request $request, Purchases $purchase)
    {
        $products = $request->get('products', []);
        $quantities = $request->get('quantities', []);

        DB::beginTransaction();
        try {
            // Crear el array para sincronizar los productos
            $syncData = [];
            $total = 0;
            foreach ($products as $index => $productId) {
                if ($productId) {
                    $product = Products::find($productId);
                    $quantity = $quantities[$index] ?? 1;
                    $subtotal = $quantity * $product->purchase_price; // Usamos purchase_price en lugar de sale_price para calcular el subtotal de la compra
                    $total += $subtotal;

                    // Actualizar el stock del producto
                    $product->stock += $quantity;
                    $product->save();

                    $syncData[$productId] = [
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            // Guardar los datos de la compra con el total calculado
            $purchase->fill(['total' => $total])->save();

            // Sincronizar los productos con sus cantidades y subtotales
            $purchase->products()->sync($syncData);

            DB::commit();

            Alert::info('Has creado/actualizado una compra con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Ocurrió un error al guardar la compra: ' . $e->getMessage());
        }

        return redirect()->route('platform.purchase.list');
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'quantities' => 'required|array'
        ]);

        $products = $request->get('products', []);
        $quantities = $request->get('quantities', []);
        $products[] = null;
        $quantities[] = 1;

        return back()->withInput(compact('products', 'quantities'));
    }
}
