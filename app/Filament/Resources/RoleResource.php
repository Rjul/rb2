<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Orchid\Platform\Dashboard;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Système';
    protected static ?string $navigationLabel = 'Rôles';
    protected static ?string $modelLabel = 'rôle';
    protected static ?string $pluralModelLabel = 'rôles';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nom')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')->required()->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\CheckboxList::make('permissions')
                ->label('Permissions')
                ->options(fn () => app(Dashboard::class)->getPermission()
                    ->collapse()
                    ->mapWithKeys(fn ($item) => [$item['slug'] => $item['description']])
                    ->toArray())
                ->columns(2)
                ->bulkToggleable()
                ->searchable()
                ->columnSpanFull()
                // Orchid stocke les permissions en map {slug: true} ; la CheckboxList travaille en liste de slugs.
                ->formatStateUsing(fn ($state) => collect($state ?? [])->filter()->keys()->all())
                ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->mapWithKeys(fn ($slug) => [$slug => true])->all()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('permissions')
                    ->label('Permissions')
                    ->getStateUsing(fn (Role $record) => collect($record->permissions ?? [])->filter()->count() . ' active(s)'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
