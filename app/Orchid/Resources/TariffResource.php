<?php

namespace App\Orchid\Resources;

use App\Models\Court;
use App\Models\Tariff;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class TariffResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Tariff::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Select::make('schedule')
                ->title('Turno')
                ->options([
                    'D' => 'Día',
                    'T' => 'Tarde',
                    'N' => 'Noche',
                ])
                ->empty('Selecciona el turno')
                ->required(),
            Input::make('price')
                ->title('Precio')
                ->placeholder('Ingresa el precio aqui')
                ->mask([
                    'alias' => 'currency',
                    'prefix' => ' ',
                    'groupSeparator' => ' ',
                    'digitsOptional' => true,
                ])
                ->required(),
            Select::make('court_id')
                ->title('Cancha')
                ->options(
                    Court::with('type_court')->get()->mapWithKeys(function ($court) {
                        return [$court->id => $court->formatted_court];
                    })->toArray()
                )
                ->empty('Selecciona la cancha')
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
            TD::make('schedule','Turno')
                ->render(function ($tariff) {
                    return $tariff->schedule_label;
                })->align(TD::ALIGN_CENTER),
            TD::make('price','Precio')->align(TD::ALIGN_CENTER),
            TD::make('court', 'Cancha')
                ->render(function ($tariff) {
                    return $tariff->court ? $tariff->court->court : 'N/A';
                })->align(TD::ALIGN_CENTER),
            TD::make('type_court', 'Tipo de cancha')
                ->render(function ($tariff) {
                    return $tariff->court && $tariff->court->type_court ? $tariff->court->type_court->type_court : 'N/A';
                })->align(TD::ALIGN_CENTER),
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
            Sight::make('schedule','Turno')
                ->render(function ($tariff) {
                    return $tariff->schedule_label;
                }),
            Sight::make('price','Precio'),
            Sight::make('court', 'Cancha')
                ->render(function ($tariff) {
                    return $tariff->court ? $tariff->court->court : 'N/A';
                }),
            Sight::make('type_court', 'Tipo de cancha')
                ->render(function ($tariff) {
                    return $tariff->court && $tariff->court->type_court ? $tariff->court->type_court->type_court : 'N/A';
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
        return 'private-tariff-resource';
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
        return 'cash'; // El ícono deseado
    }

    public static function label(): string
    {
        return __('Tarifas de canchas');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear tarifa');
    }
}
