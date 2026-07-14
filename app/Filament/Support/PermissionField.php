<?php

namespace App\Filament\Support;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;
use Orchid\Platform\Dashboard;

/**
 * Grille de permissions Orchid réutilisable (RoleResource et UserResource),
 * rendue GROUPÉE PAR CATÉGORIE comme dans Orchid (System / Contenus / Émissions…).
 *
 * Orchid stocke les permissions en map {slug: true}. Chaque catégorie est rendue
 * comme une liste de cases à cocher liée à un champ transitoire `__perm_{clé}`
 * (non persisté tel quel). Les pages utilisent le trait ManagesGroupedPermissions
 * pour éclater la map en groupes à l'affichage et la reconstruire à l'enregistrement.
 */
class PermissionField
{
    /**
     * Catégories de permissions telles que déclarées par Orchid.
     *
     * @return array<string, array{title: string, options: array<string, string>}>
     */
    public static function groups(): array
    {
        return app(Dashboard::class)->getPermission()
            ->mapWithKeys(function ($items, $title) {
                $options = collect($items)
                    ->mapWithKeys(fn ($item) => [$item['slug'] => $item['description']])
                    ->all();

                return [static::groupKey((string) $title) => [
                    'title'   => (string) $title,
                    'options' => $options,
                ]];
            })
            ->all();
    }

    /** Clé stable d'une catégorie (utilisée comme nom de champ transitoire). */
    public static function groupKey(string $title): string
    {
        return Str::slug($title) ?: 'groupe';
    }

    /** Nom du champ de formulaire transitoire portant une catégorie. */
    public static function transientKey(string $groupKey): string
    {
        return '__perm_' . $groupKey;
    }

    /**
     * Schéma des sections (une par catégorie) à insérer dans un formulaire.
     *
     * @return array<int, Section>
     */
    public static function groupedSchema(): array
    {
        $sections = [];

        foreach (static::groups() as $groupKey => $group) {
            if (empty($group['options'])) {
                continue;
            }

            $sections[] = Section::make($group['title'])
                ->compact()
                ->collapsible()
                ->schema([
                    CheckboxList::make(static::transientKey($groupKey))
                        ->hiddenLabel()
                        ->options($group['options'])
                        ->columns(2)
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->searchable(),
                ]);
        }

        return $sections;
    }

    /**
     * Grille à plat (héritage) — conservée pour compatibilité éventuelle.
     */
    public static function make(string $name = 'permissions'): CheckboxList
    {
        return CheckboxList::make($name)
            ->label('Permissions')
            ->options(fn () => app(Dashboard::class)->getPermission()->collapse()
                ->mapWithKeys(fn ($item) => [$item['slug'] => $item['description']])->all())
            ->columns(2)
            ->bulkToggleable()
            ->searchable()
            ->columnSpanFull()
            ->formatStateUsing(fn ($state) => collect($state ?? [])->filter()->keys()->all())
            ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->mapWithKeys(fn ($slug) => [$slug => true])->all());
    }
}
