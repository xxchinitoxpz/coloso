<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Products;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Modal;

class SaleEditScreen extends Screen
{
    public $sale;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Sale $sale): iterable
    {
        return [
            'sale' => $sale->load('products', 'customer'),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Create/Edit Sale';
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
                ['name' => 'Sales', 'route' => route('platform.sale.list')],
                ['name' => $this->sale->exists ? 'Edit Sale' : 'Create Sale'],
            ]]),
            Layout::rows([
                Relation::make('customer_id')
                    ->title('Customer')
                    ->fromModel(Customer::class, 'name')
                    ->required(),
                
            ]),
            ...$productFields,
            Layout::rows([
                Button::make('Add Another Product')
                    ->icon('plus')
                    ->method('addProduct'),
            ]),
            Layout::modal(
                'showSummary',
                Layout::rows([])
            )
                ->withoutApplyButton()
                ->withoutCloseButton()
                ->size(Modal::SIZE_XL)
                ->type(Modal::TYPE_RIGHT),
        ];
    }

    public function save(Request $request, Sale $sale)
    {
        $products = $request->get('products', []);
        $quantities = $request->get('quantities', []);
        $userId = Auth::id();
        $customerId = $request->get('customer_id');

        DB::beginTransaction();
        try {
            // Crear el array para sincronizar los productos
            $syncData = [];
            $total = 0;
            foreach ($products as $index => $productId) {
                if ($productId) {
                    $product = Products::find($productId);
                    $quantity = $quantities[$index] ?? 1;
                    $subtotal = $quantity * $product->sale_price;
                    $total += $subtotal;

                    // Actualizar el stock del producto
                    $product->stock -= $quantity;
                    $product->save();

                    $syncData[$productId] = [
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            // Guardar los datos de la venta con el total calculado
            $sale->fill([
                'total' => $total,
                'balance' => 0,
                'final_payment_date' => null,
                'state' => false,
                'user_id' => $userId,
                'customer_id' => $customerId,
            ])->save();

            // Sincronizar los productos con sus cantidades y subtotales
            $sale->products()->sync($syncData);

            DB::commit();

            Alert::info('You have successfully created/updated a sale.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('An error occurred while saving the sale: ' . $e->getMessage());
        }

        return redirect()->route('platform.sale.list');
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'quantities' => 'required|array'
        ]);

        $products = $request->get('products', []);
        $quantities = $request->get('quantities', []);
        $customerId = $request->get('customer_id'); // Obtener el customer_id del request
        $products[] = null;
        $quantities[] = 1;

        return back()->withInput(compact('products', 'quantities','customerId')); // Pasar el customer_id a la entrada
    }
}
