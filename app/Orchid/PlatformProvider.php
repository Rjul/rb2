<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\Emission;
use App\Models\Programme;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return [
            Menu::make('Accueil BO')
                ->route('platform.main')
                ->title('Navigation'),

            Menu::make('Accueil Site')
                ->href('/')
                ->divider(),

            Menu::make('Groupe Programmes')
                ->title('Contenues')
                ->icon('handbag')
                ->permission('platform.group.programme')
                ->route('platform.group.programme.list'),

            Menu::make('Programmes')
                ->icon('briefcase')
                ->permission('platform.programmes')
                ->route('platform.programme.list'),

            Menu::make('Emissions')
                ->slug('emissions')
                ->icon('arrow-down')
                ->list([
                    Menu::make('Liste emissions')->icon('list')
                    ->route('platform.emissions.list'),
                    Menu::make('Nouveau article')->icon('notebook'),
                    Menu::make('Nouveau audio')->icon('volume-2'),
                    Menu::make('Nouvelle video')->icon('video'),
                ]),

            Menu::make('Tags')
                ->icon('list')
                ->route('platform.tag.list')->divider(),

            Menu::make('Commentaires')
                ->title('Formulaires')
                ->icon('layers')
                ->route('platform.tag.list')
                ->badge(function () {
                    return 2;
                }),

            Menu::make('Contacts')
                ->icon('layers')
                ->route('platform.tag.list')
                ->badge(function () {
                    return 1;
                })->divider(),

            Menu::make(__('Users'))
                ->icon('user')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title('Administration'),

            Menu::make(__('Roles'))
                ->icon('lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            Menu::make('Profile')
                ->route('platform.profile')
                ->icon('user'),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {

        // Todo extend permision item for auto include all programmes
        // type : ->addProgrammesPermission(Programme)
        $programmes = Programme::all();
        $programmesRoles = ItemPermission::group(__('Emissions'));
        foreach ($programmes as $programme) {
            $programmesRoles->addPermission('platform.emission.'.$programme->id, __($programme->name));
        }
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
            ItemPermission::group(__('Contenue'))
                ->addPermission('platform.group.programme', __('Groupe de programmes'))
                ->addPermission('platform.programmes', __('Tous Programmes')),
            $programmesRoles,
        ];
    }
}
