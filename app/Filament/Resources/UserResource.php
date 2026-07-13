<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Support\PermissionField;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Système';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'utilisateur';
    protected static ?string $pluralModelLabel = 'utilisateurs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profil')
                ->description("Renseigner le nom et l'email de l'utilisateur.")
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')->email()->required()
                        ->unique(ignoreRecord: true)->maxLength(255),
                ]),

            Forms\Components\Section::make('Mot de passe')
                ->description('Laisser vide pour conserver le mot de passe actuel.')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()->revealable()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create'),
                ]),

            Forms\Components\Section::make('Rôles')
                ->description('Un rôle définit un ensemble de tâches autorisées.')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Rôles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload(),
                ]),

            Forms\Components\Section::make('Permissions directes')
                ->description("Permissions accordées à cet utilisateur en plus de celles de ses rôles.")
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
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Rôles')->badge(),
                Tables\Columns\IconColumn::make('email_verified_at')->label('Vérifié')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Modifié le')->dateTime('d/m/Y H:i')->sortable()->toggleable(),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
