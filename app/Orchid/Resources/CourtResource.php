<?php

namespace App\Orchid\Resources;

use App\Models\Court;
use App\Models\TypeCourt;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class CourtResource extends Resource
{
    /**
     * El modelo al que corresponde el recurso.
     *
     * @var string
     */
    public static $model = Court::class;

    /**
     * Obtén los campos que se mostrarán en el recurso.
     *
     * @return array
     */
    public function fields(): array
    {
        return [

            Input::make('court')
                ->title('Cancha')
                ->placeholder('Ingresa el nombre de la cancha aquí')
                ->required(),

            CheckBox::make('state')
                ->title('Estado')
                ->placeholder('¿La cancha está libre?')
                ->sendTrueOrFalse(),

            Select::make('type_court_id')
                ->title('Tipo de Cancha')
                ->options(TypeCourt::pluck('type_court', 'id')->toArray())
                ->empty('Selecciona el tipo de cancha')
                ->required(),

        ];
    }

    /**
     * Obtén las columnas que se mostrarán en el recurso.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id')->align(TD::ALIGN_CENTER),

            TD::make('court', 'Cancha')
                ->sort()
                ->render(function ($court) {
                    return $court->court;
                })->align(TD::ALIGN_CENTER),

            TD::make('type_court', 'Tipo de Cancha')
                ->render(function ($court) {
                    return $court->type_court ? $court->type_court->type_court : 'N/A';
                })->align(TD::ALIGN_CENTER),

            TD::make('state', 'Estado')
                ->render(function ($court) {
                    return $court->state ? 'Libre' : 'No libre';
                })
                ->sort()->align(TD::ALIGN_CENTER),


            TD::make('created_at', 'Fecha de creación')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),

            TD::make('updated_at', 'Fecha de actualización')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),
        ];
    }

    /**
     * Obtén los detalles que se mostrarán en el recurso.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id'),
            Sight::make('court'),
            Sight::make('type_court', 'Tipo de Cancha')
                ->render(function ($court) {
                    return $court->type_court ? $court->type_court->type_court : 'N/A';
                }),

            Sight::make('state', 'Estado')
                ->render(function ($court) {
                    return $court->state ? 'Libre' : 'No libre';
                })
                ->sort(),
        ];
    }

    /**
     * Obtén los filtros disponibles para el recurso.
     *
     * @return array
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Obtén la clave de permiso para el recurso.
     *
     * @return string|null
     */
    public static function permission(): ?string
    {
        return 'private-court-resource';
    }

    /**
     * Obtén el número de modelos a devolver por página.
     *
     * @return int
     */
    public static function perPage(): int
    {
        return 10;
    }

    public static function icon(): string
    {
        return 'dribbble'; // El ícono deseado
    }

    public static function label(): string
    {
        return __('Canchas');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear cancha');
    }
}
