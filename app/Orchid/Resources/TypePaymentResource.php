<?php

namespace App\Orchid\Resources;

use App\Models\TypePayment;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Facades\Dashboard;

class TypePaymentResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = TypePayment::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('type_payment')
                ->title('Tipo de pago')
                ->placeholder('Ingresa el tipo de pago aqui')
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

            TD::make('type_payment', 'Tipo de pago')->align(TD::ALIGN_CENTER),

            TD::make('created_at', 'Fecha de creación')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),

            TD::make('updated_at', 'Fecha de ultima modificación')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),
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
            Sight::make('type_payment','Tipo de pago'),
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
        return 'private-typepayment-resource';
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
        return 'wallet'; // El ícono deseado
    }
    public static function label(): string
    {
        return __('Tipos de pagos');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear tipo pago');
    }
}
