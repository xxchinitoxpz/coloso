<?php

namespace App\Orchid\Resources;

use App\Models\Customer;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class CustomersResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Customer::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('name')
                ->title('Nombre')
                ->placeholder('Ingresa el nombre aqui')
                ->required(),
            Input::make('dni')
                ->title('DNI')
                ->placeholder('Ingresa el dni aqui')
                ->required(),
            Input::make('phone')
                ->title('Telefono')
                ->placeholder('Ingresa el telefono aqui')
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

            TD::make('name','Nombre')->align(TD::ALIGN_CENTER),

            TD::make('dni','DNI')->align(TD::ALIGN_CENTER),

            TD::make('phone','Telefono')->align(TD::ALIGN_CENTER),

            TD::make('created_at', 'Fecha de creación')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),

            // TD::make('updated_at', 'Update date')
            //     ->render(function ($model) {
            //         return $model->updated_at->toDateTimeString();
            //     }),
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
            Sight::make('name','Nombre'),
            Sight::make('dni','DNI'),
            Sight::make('phone','Telefono'),
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
        return 'private-customer-resource';
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
        return 'people'; // El ícono deseado
    }
    
    public static function label(): string
    {
        return __('Clientes');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear cliente');
    }

}
