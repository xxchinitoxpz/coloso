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

            Input::make('name')
                ->title('Product')
                ->placeholder('Enter product here')
                ->required(),

            Input::make('purchase_price')
                ->title('Purchase price')
                ->placeholder('Enter purchase price here')
                ->mask([
                    'alias' => 'currency',
                    'prefix' => ' ',
                    'groupSeparator' => ' ',
                    'digitsOptional' => true,
                ])
                ->required(),

            Input::make('sale_price')
                ->title('Sale Price')
                ->placeholder('Enter sale price here')
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
                ->placeholder('Enter stock here')
                ->required(),

            CheckBox::make('state')
                ->title('State')
                ->placeholder('Is the product active?')
                ->sendTrueOrFalse(),
            Select::make('categorie_id')
                ->title('Category')
                ->options(Categories::pluck('categorie', 'id')->toArray())
                ->empty('Select category')
                ->required(),

            Select::make('brand_id')
                ->title('Brand')
                ->options(Brands::pluck('brand', 'id')->toArray())
                ->empty('Select brand')
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
            TD::make('id'),

            TD::make('name', 'Product Name')
                ->sort()
                ->render(function ($product) {
                    return $product->name;
                }),

            TD::make('purchase_price', 'Purchase Price')
                ->sort()
                ->render(function ($product) {
                    return number_format($product->purchase_price, 2);
                }),

            TD::make('sale_price', 'Sale Price')
                ->sort()
                ->render(function ($product) {
                    return number_format($product->sale_price, 2);
                }),

            TD::make('stock', 'Stock')
                ->sort()
                ->render(function ($product) {
                    return $product->stock;
                }),

            TD::make('state', 'State')
                ->render(function ($product) {
                    return $product->state ? 'Active' : 'Inactive';
                })
                ->sort(),

            TD::make('created_at', 'Date of creation')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                }),

            TD::make('updated_at', 'Update date')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                }),
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
            Sight::make('id'),
            Sight::make('name'),
            Sight::make('purchase_price', 'Purchase Price')

                ->render(function ($product) {
                    return number_format($product->purchase_price, 2);
                }),

            Sight::make('sale_price', 'Sale Price')

                ->render(function ($product) {
                    return number_format($product->sale_price, 2);
                }),

            Sight::make('stock', 'Stock')
                ->render(function ($product) {
                    return $product->stock;
                }),

            Sight::make('state', 'State')
                ->render(function ($product) {
                    return $product->state ? 'Active' : 'Inactive';
                }),
            Sight::make('categorie', 'Categorie')
                ->render(function ($product) {
                    return $product->categorie ? $product->categorie->categorie : 'N/A';
                }),

            Sight::make('brand', 'Brand')
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
}
