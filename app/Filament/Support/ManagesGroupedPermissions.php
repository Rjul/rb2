<?php

namespace App\Filament\Support;

/**
 * Fait le pont entre la map de permissions Orchid `{slug: true}` (stockée sur le
 * modèle) et les champs de formulaire groupés par catégorie (voir PermissionField).
 *
 * - hydratePermissionGroups : map → un tableau de slugs cochés par catégorie (à l'affichage).
 * - collectPermissionGroups : les cases cochées de chaque catégorie → map (à l'enregistrement).
 */
trait ManagesGroupedPermissions
{
    protected function hydratePermissionGroups(array $data): array
    {
        // Lit la map depuis le modèle directement : sur User, `permissions` est
        // masqué ($hidden) et absent de attributesToArray() → donc de $data.
        $permissions = collect($this->record?->permissions ?? $data['permissions'] ?? []);

        foreach (PermissionField::groups() as $groupKey => $group) {
            $data[PermissionField::transientKey($groupKey)] = collect($group['options'])
                ->keys()
                ->filter(fn ($slug) => (bool) ($permissions[$slug] ?? false))
                ->values()
                ->all();
        }

        return $data;
    }

    protected function collectPermissionGroups(array $data): array
    {
        $permissions = [];

        foreach (PermissionField::groups() as $groupKey => $group) {
            $key = PermissionField::transientKey($groupKey);

            foreach ((array) ($data[$key] ?? []) as $slug) {
                $permissions[$slug] = true;
            }

            unset($data[$key]);
        }

        $data['permissions'] = $permissions;

        return $data;
    }
}
