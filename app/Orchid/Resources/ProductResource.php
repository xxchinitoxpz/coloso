<?php

namespace App\Orchid\Resources;

use App\Models\Brands;
use App\Models\Categories;
use App\Models\Products;
use Orchid\Crud\Resource;
use Orchid\Screen\Components\Cells\Number;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class ProductResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Products::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [

            Input::make('name','Nombre')
                ->title('Product')
                ->placeholder('Ingresa el nombre aqui')
                ->required(),

            Input::make('purchase_price')
                ->title('Precio de compra')
                ->placeholder('Ingresa el precio de compra aqui')
                ->mask([
                    'alias' => 'currency',
                    'prefix' => ' ',
                    'groupSeparator' => ' ',
                    'digitsOptional' => true,
                ])
                ->required(),

            Input::make('sale_price')
                ->title('Precio de venta')
                ->placeholder('Ingresa el precio de venta aqui')
                ->mask([
                    'alias' => 'currency',
                    'prefix' => ' ',
                    'groupSeparator' => ' ',
                    'digitsOptional' => true,
                ])
                ->required(),

            Input::make('stock')
                ->type('Number')
                ->title('Stock')
                ->placeholder('Ingresa el stock aqui')
                ->required(),

            CheckBox::make('state')
                ->title('Estado')
                ->placeholder('Este producto esta activo?')
                ->sendTrueOrFalse(),
            Select::make('categorie_id')
                ->title('Categoria')
                ->options(Categories::pluck('categorie', 'id')->toArray())
                ->empty('Seleccione categoria')
                ->required(),

            Select::make('brand_id')
                ->title('Marca')
                ->options(Brands::pluck('brand', 'id')->toArray())
                ->empty('Seleccione marca')
                ->required(),
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id','ID')->align(TD::ALIGN_CENTER),

            TD::make('name', 'Nombre producto')
                ->sort()
                ->render(function ($product) {
                    return $product->name;
                })->align(TD::ALIGN_CENTER),

            TD::make('purchase_price', 'Precio compra')
                ->sort()
                ->render(function ($product) {
                    return number_format($product->purchase_price, 2);
                })->align(TD::ALIGN_CENTER),

            TD::make('sale_price', 'Precio venta')
                ->sort()
                ->render(function ($product) {
                    return number_format($product->sale_price, 2);
                })->align(TD::ALIGN_CENTER),

            TD::make('stock', 'Stock')
                ->sort()
                ->render(function ($product) {
                    return $product->stock;
                })->align(TD::ALIGN_CENTER),

            TD::make('state', 'Estado')
                ->render(function ($product) {
                    return $product->state ? 'Active' : 'Inactive';
                })
                ->sort()->align(TD::ALIGN_CENTER),

            TD::make('created_at', 'Fecha de creación')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),

            // TD::make('updated_at', 'Update date')
            //     ->render(function ($model) {
            //         return $model->updated_at->toDateTimeString();
            //     })->align(TD::ALIGN_CENTER),
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id','ID'),
            Sight::make('name','Nombre producto'),
            Sight::make('purchase_price', 'Precio compra')

                ->render(function ($product) {
                    return number_format($product->purchase_price, 2);
                }),

            Sight::make('sale_price', 'Precio venta')

                ->render(function ($product) {
                    return number_format($product->sale_price, 2);
                }),

            Sight::make('stock', 'Stock')
                ->render(function ($product) {
                    return $product->stock;
                }),

            Sight::make('state', 'Estado')
                ->render(function ($product) {
                    return $product->state ? 'Active' : 'Inactive';
                }),
            Sight::make('categorie', 'Categoria')
                ->render(function ($product) {
                    return $product->categorie ? $product->categorie->categorie : 'N/A';
                }),

            Sight::make('brand', 'Marca')
                ->render(function ($product) {
                    return $product->brand ? $product->brand->brand : 'N/A';
                }),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Get the permission key for the resource.
     *
     * @return string|null
     */
    public static function permission(): ?string
    {
        return 'private-product-resource';
    }

    /**
     * Get the number of models to return per page
     *
     * @return int
     */
    public static function perPage(): int
    {
        return 10;
    }

    public static function icon(): string
    {
        return 'cup-straw'; // El ícono deseado
    }

    public static function label(): string
    {
        return __('Productos');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear productos');
    }
}
