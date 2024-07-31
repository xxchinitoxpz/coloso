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
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Court::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [

            Input::make('court')
                ->title('Court')
                ->placeholder('Enter court here')
                ->required(),

            CheckBox::make('state')
                ->title('State')
                ->placeholder('Is the court free?')
                ->sendTrueOrFalse(),

            Select::make('type_court_id')
                ->title('Type Court')
                ->options(TypeCourt::pluck('type_court', 'id')->toArray())
                ->empty('Select Type Court')
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
            TD::make('id')->align(TD::ALIGN_CENTER),

            TD::make('court', 'Court')
                ->sort()
                ->render(function ($court) {
                    return $court->court;
                })->align(TD::ALIGN_CENTER),

            TD::make('type_court', 'Type Court')
                ->render(function ($court) {
                    return $court->type_court ? $court->type_court->type_court : 'N/A';
                })->align(TD::ALIGN_CENTER),

            TD::make('state', 'State')
                ->render(function ($court) {
                    return $court->state ? 'Free' : 'Not free';
                })
                ->sort()->align(TD::ALIGN_CENTER),


            TD::make('created_at', 'Date of creation')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                })->align(TD::ALIGN_CENTER),

            TD::make('updated_at', 'Update date')
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
            Sight::make('id'),
            Sight::make('court'),
            Sight::make('type_court', 'Type Court')
                ->render(function ($court) {
                    return $court->type_court ? $court->type_court->type_court : 'N/A';
                }),

            Sight::make('state', 'State')
                ->render(function ($court) {
                    return $court->state ? 'Free' : 'Not free';
                })
                ->sort(),
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
        return 'private-court-resource';
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
        return 'dribbble'; // El ícono deseado
    }
}
