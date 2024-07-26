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
            Input::make('schedule')
                ->title('Schedule')
                ->placeholder('Enter schedule here')
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
            TD::make('court', 'Court')
                ->render(function ($tariff) {
                    return $tariff->court ? $tariff->court->court : 'N/A';
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
        return [];
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
}
