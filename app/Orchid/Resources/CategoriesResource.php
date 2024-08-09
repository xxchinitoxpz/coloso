<?php

namespace App\Orchid\Resources;

use App\Models\Categories;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class CategoriesResource extends Resource
{
    /**
     * El modelo al que corresponde el recurso.
     *
     * @var string
     */
    public static $model = Categories::class;

    /**
     * Obtén los campos que se mostrarán en el recurso.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('categorie')
                ->title('Categoría')
                ->placeholder('Ingresa la categoría aquí'),
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

            TD::make('categorie', 'Categoría')->align(TD::ALIGN_CENTER),

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
            Sight::make('categorie', 'Categoría'),
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
        return 'private-categorie-resource';
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

    public static function label(): string
    {
        return __('Categorias - Producto');
    }

    public static function createButtonLabel(): string
    {
        return __('Crear categoria');
    }
}
