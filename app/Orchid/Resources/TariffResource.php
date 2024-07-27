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
                ->title('Schedule')
                ->options([
                    'D' => 'Día',
                    'T' => 'Tarde',
                    'N' => 'Noche',
                ])
                ->empty('Select schedule')
                ->required(),
            Input::make('price')
                ->title('Price')
                ->placeholder('Enter price here')
                ->mask([
                    'alias' => 'currency',
                    'prefix' => ' ',
                    'groupSeparator' => ' ',
                    'digitsOptional' => true,
                ])
                ->required(),
            Select::make('court_id')
                ->title('Court')
                ->options(
                    Court::with('type_court')->get()->mapWithKeys(function ($court) {
                        return [$court->id => $court->formatted_court];
                    })->toArray()
                )
                ->empty('Select court')
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
            TD::make('schedule')
                ->render(function ($tariff) {
                    return $tariff->schedule_label;
                }),
            TD::make('price'),
            TD::make('court', 'Court')
                ->render(function ($tariff) {
                    return $tariff->court ? $tariff->court->court : 'N/A';
                }),
            TD::make('type_court', 'Type of Court')
                ->render(function ($tariff) {
                    return $tariff->court && $tariff->court->type_court ? $tariff->court->type_court->type_court : 'N/A';
                }),
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
            Sight::make('schedule')
                ->render(function ($tariff) {
                    return $tariff->schedule_label;
                }),
            Sight::make('price'),
            Sight::make('court', 'Court')
                ->render(function ($tariff) {
                    return $tariff->court ? $tariff->court->court : 'N/A';
                }),
            Sight::make('type_court', 'Type of Court')
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
}
