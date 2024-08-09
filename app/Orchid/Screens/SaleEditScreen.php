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

class SaleEditScreen extends Screen
{
    public $sale;

    /**
     * Obtener los datos a mostrar en la pantalla.
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
     * El nombre de la pantalla mostrado en el encabezado.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Crear/Editar Venta';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.sales',
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
        $customerId = old('customer_id', $this->sale->customer_id ?? null);

        $productFields = [];
        foreach ($products as $index => $product) {
            $productFields[] = Layout::rows([
                Relation::make("products[$index]")
                    ->title('Producto')
                    ->fromModel(Products::class, 'name', 'id')
                    ->displayAppend('priceDisplay')
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
                ['name' => 'Ventas', 'route' => route('platform.sale.list')],
                ['name' => $this->sale->exists ? 'Editar Venta' : 'Crear Venta'],
            ]]),
            Layout::rows([
                Relation::make('customer_id')
                    ->title('Cliente')
                    ->fromModel(Customer::class, 'name')
                    ->required()
                    ->value($customerId),
            ]),
            ...$productFields,
            Layout::rows([
                Button::make('Añadir Otro Producto')
                    ->icon('plus')
                    ->method('addProduct'),
            ])
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

            Alert::info('Has creado/actualizado la venta con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Ocurrió un error al guardar la venta: ' . $e->getMessage());
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

        return back()->withInput(compact('products', 'quantities', 'customerId'));
    }
}
