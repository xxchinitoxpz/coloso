<?php

namespace App\Orchid\Screens;

use App\Models\Purchases;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;

class PurchaseListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'purchases' => Purchases::orderBy('created_at', 'desc')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'PurchaseListScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Purchase')
                ->icon('plus')
                ->route('platform.purchase.create'),
                
        ];
    }
    
    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('breadcrumbs', ['breadcrumbs' => [
                ['name' => 'Home', 'route' => route('platform.main')],
                ['name' => 'Purchases', 'route' => route('platform.purchase.list')],
            ]]),
            Layout::table('purchases', [
                TD::make('id', 'ID'),
                TD::make('total', 'Total'),
                TD::make('created_at', 'Created At'),
                TD::make('updated_at', 'Updated At'),
                TD::make('details', 'Details')
                    ->render(function (Purchases $purchase) {
                        return Link::make('')
                            ->icon('eye')
                            ->route('platform.purchase.details', $purchase->id);
                    }),
            ]),
        ];
    }
      
    
}
