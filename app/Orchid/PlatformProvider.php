<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\Comment;
use App\Models\Emission;
use App\Models\Programme;
use App\Models\WebsiteNew;
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
            Menu::make('Accueil Site')
                ->href('/')
                ->divider(),

            Menu::make('Liste emissions')->icon('list')
                ->route('platform.emissions.list'),
            Menu::make('Nouveau article')->icon('notebook')
                ->route('platform.emission.text.edit'),
            Menu::make('Nouveau audio')->icon('volume-2')
                ->route('platform.emission.edit'),
            Menu::make('Nouvelle video')->icon('video')
                ->route('platform.emission.video.edit')->divider(),

            Menu::make('Contenues')
                ->slug('contenues')
                ->icon('arrow-down')
                ->list([
                    Menu::make('Groupes de Programme')
                        ->title('Contenues')
                        ->icon('handbag')
                        ->permission('platform.group.programme')
                        ->route('platform.group.programme.list'),
                    Menu::make('Programmes')
                        ->icon('briefcase')
                        ->route('platform.programme.list'),
                    Menu::make('Annonces')
                        ->icon('layers')
                        ->route('platform.annonces.list')
                        ->badge(function () {
                            return count(WebsiteNew::getActive());
                        }),
                    Menu::make('Tags')
                        ->icon('list')
                        ->route('platform.tag.list')
                ])->divider(),

            Menu::make('Commentaires')
                ->title('Formulaires')
                ->icon('layers')
                ->route('platform.comments.list')
                ->badge(function () {
                    return Comment::query()->selectRaw('COUNT(id) as count')
                        ->where('approved', 0)->get()
                        ->pluck('count')->first();
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
