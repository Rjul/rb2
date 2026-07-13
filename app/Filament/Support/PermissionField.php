<?php

namespace App\Filament\Support;

use Filament\Forms\Components\CheckboxList;
use Orchid\Platform\Dashboard;

/**
 * Grille de permissions Orchid réutilisable (RoleResource et UserResource).
 * Orchid stocke les permissions en map {slug: true} ; la CheckboxList travaille
 * en liste de slugs — la conversion se fait via format/dehydrate.
 */
class PermissionField
{
    public static function make(string $name = 'permissions'): CheckboxList
    {
        return CheckboxList::make($name)
            ->label('Permissions')
            ->options(fn () => static::options())
            ->descriptions(fn () => app(Dashboard::class)->getPermission()->collapse()
                ->mapWithKeys(fn ($item) => [$item['slug'] => $item['slug']])->all())
            ->columns(2)
            ->bulkToggleable()
            ->searchable()
            ->columnSpanFull()
            ->formatStateUsing(fn ($state) => collect($state ?? [])->filter()->keys()->all())
            ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->mapWithKeys(fn ($slug) => [$slug => true])->all());
    }

    protected static function options(): array
    {
        return app(Dashboard::class)->getPermission()
            ->collapse()
            ->mapWithKeys(fn ($item) => [$item['slug'] => $item['description']])
            ->all();
    }
}
