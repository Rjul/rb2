<?php

namespace App\Filament\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Reproduit le gating d'Orchid dans Filament : un slug de permission unique
 * garde l'accès à toute la Resource (navigation + pages index/create/edit).
 * Les droits se cumulent (permissions directes ∪ permissions des rôles) via
 * le hasAccess() d'Orchid, donc le comportement est identique aux deux back-offices.
 *
 * Une Resource l'utilise en déclarant le slug attendu :
 *
 *     use GatesAccessByPermission;
 *     protected static function permissionSlug(): ?string { return 'platform.themes'; }
 *
 * Retourner null (défaut) = accessible à tout utilisateur pouvant entrer dans le panel.
 */
trait GatesAccessByPermission
{
    protected static function permissionSlug(): ?string
    {
        return null;
    }

    protected static function userHasPermission(): bool
    {
        $slug = static::permissionSlug();

        if ($slug === null) {
            return true;
        }

        return (bool) Auth::user()?->hasAccess($slug);
    }

    public static function canViewAny(): bool
    {
        return static::userHasPermission();
    }

    public static function canAccess(): bool
    {
        return static::userHasPermission();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::userHasPermission();
    }
}
