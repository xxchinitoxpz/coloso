<?php

namespace App\Orchid\Resources;

use App\Models\TypeCourt;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class TypeCourtResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = TypeCourt::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('type_court')
                ->title('Tipo de cancha')
                ->placeholder('Ingresa el tipo de cancha aqui')
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

            TD::make('type_court', 'Tipo de cancha')->align(TD::ALIGN_CENTER),

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
            Sight::make('type_court','Tipo de cancha'),
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
        return 'private-typecourt-resource';
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
        return 'bricks'; // El ícono deseado
    }

    public static function label(): string
    {
        return __('Tipos de canchas');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear tipo cancha');
    }
}
