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
     * Fetch data to be displayed on the screen.
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
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Create/Edit Purchase';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('save'),
            // ModalToggle::make('Summary')
            //     ->icon('eye')
            //     ->modal('showSummary'),

        ];
    }

    /**
     * The screen's layout elements.
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
                    ->title('Product')
                    ->fromModel(Products::class, 'name')
                    ->required(),
                Input::make("quantities[$index]")
                    ->title('Quantity')
                    ->type('number')
                    ->required()
                    ->step(1)
                    ->value($quantities[$index]),
            ]);
        }

        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Home', 'route' => route('platform.main')],
                ['name' => 'Purchases', 'route' => route('platform.purchase.list')],
                ['name' => $this->purchase->exists ? 'Edit Purchase' : 'Create Purchase'],
            ]]),
            ...$productFields,
            Layout::rows([
                Button::make('Add Another Product')
                    ->icon('plus')
                    ->method('addProduct'),
            ]),
            Layout::modal(
                'showSummary',
                Layout::rows([
                    
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

            Alert::info('You have successfully created/updated a purchase.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('An error occurred while saving the purchase: ' . $e->getMessage());
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
