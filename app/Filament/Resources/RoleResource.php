<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Support\GatesAccessByPermission;
use App\Filament\Support\PermissionField;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use GatesAccessByPermission;

    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Système';
    protected static ?string $navigationLabel = 'Rôles';
    protected static ?string $modelLabel = 'rôle';
    protected static ?string $pluralModelLabel = 'rôles';

    protected static function permissionSlug(): ?string
    {
        return 'platform.systems.roles';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Rôle')
                ->description('Un rôle est un ensemble de privilèges accordés aux utilisateurs qui le portent.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')->required()->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')->required()->maxLength(255)
                        ->helperText('Identifiant système unique, sans espace.')
                        ->unique(ignoreRecord: true),
                ]),

            Forms\Components\Section::make('Permissions / Privilèges')
                ->description('Privilèges nécessaires pour effectuer certaines tâches.')
                ->schema([
                    PermissionField::make('permissions'),
                ]),
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
                    ->getStateUsing(fn (Role $record) => collect($record->permissions ?? [])->filter()->count() . ' active(s)')
                    ->badge(),
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
