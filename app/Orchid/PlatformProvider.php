<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;


class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Home')
                ->icon('bs.book')
                ->title('Navigation')
                ->route(config('platform.index')),

            // Menu::make('Sample Screen')
            //     ->icon('bs.collection')
            //     ->route('platform.example')
            //     ->badge(fn () => 6),

            // Menu::make('Form Elements')
            //     ->icon('bs.card-list')
            //     ->route('platform.example.fields')
            //     ->active('*/examples/form/*'),

            // Menu::make('Overview Layouts')
            //     ->icon('bs.window-sidebar')
            //     ->route('platform.example.layouts'),

            // Menu::make('Grid System')
            //     ->icon('bs.columns-gap')
            //     ->route('platform.example.grid'),

            // Menu::make('Charts')
            //     ->icon('bs.bar-chart')
            //     ->route('platform.example.charts'),

            // Menu::make('Cards')
            //     ->icon('bs.card-text')
            //     ->route('platform.example.cards')
            //     ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            Menu::make('Reports')
                ->icon('archive')
                ->title(__('Reports of coloso'))
                ->list([
                    Menu::make('Sales of the day')
                    ->icon('bs.bag')
                    ->sort(2)
                    ->route('platform.sales.list.today'),
                    Menu::make('Total debts')
                    ->icon('bs.calculator')
                    ->sort(0)
                    ->route('platform.total.debts'),
                ])
                ->divider(),

            Menu::make(__('Purchases'))
                ->icon('bs.basket')
                ->route('platform.purchase.list')
                ->permission('platform.systems.purchases')
                ->title(__('Processes of coloso')),
            Menu::make(__('Sales'))
                ->icon('bs.cart-plus')
                ->route('platform.sale.list')
                ->permission('platform.systems.sales'),

            Menu::make(__('Sales payments'))
                ->icon('bs.credit-card')
                ->route('platform.payment.list')
                ->permission('platform.systems.payments')
                ->divider(),

                Menu::make(__('Rentals and Reservations'))
                ->icon('bs.cookie')
                ->route('platform.rental.list')
                ->permission('platform.systems.rentals')
                ->divider(),
            // Menu::make('Documentation')
            //     ->title('Docs')
            //     ->icon('bs.box-arrow-up-right')
            //     ->url('https://orchid.software/en/docs')
            //     ->target('_blank'),

            // Menu::make('Changelog')
            //     ->icon('bs.box-arrow-up-right')
            //     ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
            //     ->target('_blank')
            //     ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
            ItemPermission::group(__('Processes'))
                ->addPermission('platform.systems.purchases', __('Purchase'))
                ->addPermission('platform.systems.sales', __('Sale'))
                ->addPermission('platform.systems.rentals', __('Rental'))
                ->addPermission('platform.systems.payments', __('Payment')),

        ];
    }
}
