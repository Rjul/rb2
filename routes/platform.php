<?php

declare(strict_types=1);


use App\Orchid\Screens\HomeBackOffice;

use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;

use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;

use App\Orchid\Screens\Tags\TagEditScreen;
use App\Orchid\Screens\Tags\TagsListScreen;

use App\Orchid\Screens\Programme\ProgrammeEditScreen;
use App\Orchid\Screens\Programme\ProgrammeListScreen;


use App\Orchid\Screens\Comment\CommentListScreen;




use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

Route::screen('/main', HomeBackOffice::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Profile'), route('platform.profile'));
    });

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(function (Trail $trail, $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('User'), route('platform.systems.users.edit', $user));
    });

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('Create'), route('platform.systems.users.create'));
    });

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Users'), route('platform.systems.users'));
    });

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(function (Trail $trail, $role) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Role'), route('platform.systems.roles.edit', $role));
    });

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Create'), route('platform.systems.roles.create'));
    });

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Roles'), route('platform.systems.roles'));
    });


/*
|--------------------------------------------------------------------------
| Écrans create/edit séparés (contrainte Orchid 14)
|--------------------------------------------------------------------------
| Avant : une seule route `entite/{param?}` servait création ET édition.
| Orchid 14 résout les bindings implicites AVANT d'appeler la méthode de
| l'écran : en création, le POST `entite/createOrUpdate` faisait atterrir
| le nom de méthode dans `{param}` → ModelNotFound → 404. On suit donc le
| schéma officiel (comme users/roles ci-dessus) : `create` dédié + param
| requis pour l'édition, et une redirection depuis l'ancienne URL nue.
*/

//=======================================================
// Tags                                                 |
//=======================================================
Route::screen('tag/create', TagEditScreen::class)
    ->name('platform.tag.create');
Route::screen('tag/{tag}', TagEditScreen::class)
    ->name('platform.tag.edit');
Route::redirect('tag', \Orchid\Platform\Dashboard::prefix('/tag/create'));

Route::screen('tags', TagsListScreen::class)
    ->name('platform.tag.list');


//=======================================================
// Programme                                            |
//=======================================================
Route::screen('programme/create', ProgrammeEditScreen::class)
    ->name('platform.programme.create');
Route::screen('programme/{programme}', ProgrammeEditScreen::class)
    ->name('platform.programme.edit');
Route::redirect('programme', \Orchid\Platform\Dashboard::prefix('/programme/create'));

Route::screen('programmes', ProgrammeListScreen::class)
    ->name('platform.programme.list');

//=======================================================
// Page Admin                                          |
//=======================================================
Route::screen('page-admin/create', \App\Orchid\Screens\PageAdmin\PageAdminEditScreen::class)
    ->name('platform.page-admin.create');
Route::screen('page-admin/{pageAdmin}', \App\Orchid\Screens\PageAdmin\PageAdminEditScreen::class)
    ->name('platform.page-admin.edit');
Route::redirect('page-admin', \Orchid\Platform\Dashboard::prefix('/page-admin/create'));

Route::screen('pages-admin', \App\Orchid\Screens\PageAdmin\PageAdminListScreen::class)
    ->name('platform.page-admin.list');


//=======================================================
// Group Programme                                      |
//=======================================================
Route::screen('group/programme/create', \App\Orchid\Screens\GroupProgramme\GroupProgrammeEditScreen::class)
    ->name('platform.group.programme.create');
Route::screen('group/programme/{programme}', \App\Orchid\Screens\GroupProgramme\GroupProgrammeEditScreen::class)
    ->name('platform.group.programme.edit');
Route::redirect('group/programme', \Orchid\Platform\Dashboard::prefix('/group/programme/create'));

Route::screen('group/programmes', \App\Orchid\Screens\GroupProgramme\GroupProgrammeListScreen::class)
    ->name('platform.group.programme.list');

//=======================================================
// Emission                                     |
//=======================================================
Route::screen('emission/audio/create', \App\Orchid\Screens\Emission\EmissionEditScreen::class)
    ->name('platform.emission.create');
Route::screen('emission/audio/{emission}', \App\Orchid\Screens\Emission\EmissionEditScreen::class)
    ->name('platform.emission.edit');
Route::redirect('emission/audio', \Orchid\Platform\Dashboard::prefix('/emission/audio/create'));

Route::screen('emission/video/create', \App\Orchid\Screens\Emission\EmissionVideoEditScreen::class)
    ->name('platform.emission.video.create');
Route::screen('emission/video/{emission}', \App\Orchid\Screens\Emission\EmissionVideoEditScreen::class)
    ->name('platform.emission.video.edit');
Route::redirect('emission/video', \Orchid\Platform\Dashboard::prefix('/emission/video/create'));

Route::screen('emission/text/create', \App\Orchid\Screens\Emission\EmissionTextEditScreen::class)
    ->name('platform.emission.text.create');
Route::screen('emission/text/{emission}', \App\Orchid\Screens\Emission\EmissionTextEditScreen::class)
    ->name('platform.emission.text.edit');
Route::redirect('emission/text', \Orchid\Platform\Dashboard::prefix('/emission/text/create'));

Route::screen('emissions', \App\Orchid\Screens\Emission\EmissionListScreen::class)
    ->name('platform.emissions.list');

//=======================================================
// Annonce                                              |
//=======================================================
Route::screen('annonce/create', \App\Orchid\Screens\WebsiteNewEditScreen::class)
    ->name('platform.annonce.create');
Route::screen('annonce/{websiteNew}', \App\Orchid\Screens\WebsiteNewEditScreen::class)
    ->name('platform.annonce.edit');
Route::redirect('annonce', \Orchid\Platform\Dashboard::prefix('/annonce/create'));

Route::screen('annonces', \App\Orchid\Screens\WebsiteNewListScreen::class)
    ->name('platform.annonces.list');

//=======================================================
// Comments                                             |
//=======================================================
//Route::screen('commentaires/{websiteNew?}', \App\Orchid\Screens\WebsiteNewEditScreen::class)
//    ->name('platform.annonce.edit');

Route::screen('commentaires', CommentListScreen::class)
    ->name('platform.comments.list');

Route::get('commentaires/inverse/{comment:id}', [\App\Http\Controllers\CommentController::class, 'inverseApproved'])
    ->name('platform.comment.approved');
Route::get('commentaires/delete/{comment:id}', [\App\Http\Controllers\CommentController::class, 'delete'])
    ->name('platform.comment.delete');



