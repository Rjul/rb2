<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Support\GatesAccessByPermission;
use App\Filament\Support\PermissionField;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Orchid\Access\Impersonation;

class UserResource extends Resource
{
    use GatesAccessByPermission;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Système';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'utilisateur';
    protected static ?string $pluralModelLabel = 'utilisateurs';

    protected static function permissionSlug(): ?string
    {
        return 'platform.systems.users';
    }

    /**
     * Configure l'action « Se connecter en tant que » (impersonation, comme Orchid).
     * Partagée entre l'action de ligne (liste) et l'entête de la page d'édition.
     * On redirige vers la gestion si l'utilisateur usurpé peut y accéder, sinon
     * vers le site ; un bandeau « Revenir à mon compte » reste affiché dans le panel.
     *
     * @template T of \Filament\Actions\Action|\Filament\Tables\Actions\Action
     * @param  T  $action
     * @return T
     */
    public static function configureImpersonateAction($action)
    {
        return $action
            ->label('Se connecter en tant que')
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Usurper cette identité')
            ->modalDescription("Vous naviguerez en tant que cet utilisateur. Vous pourrez revenir à votre compte via le bandeau « Revenir à mon compte » affiché dans la gestion.")
            ->modalSubmitActionLabel('Se connecter')
            ->visible(fn (User $record) => Auth::id() !== $record->id)
            ->action(function (User $record) {
                Impersonation::loginAs($record);

                Notification::make()
                    ->title("Vous usurpez maintenant l'identité de {$record->name}")
                    ->warning()
                    ->send();

                return redirect($record->hasAccess('platform.index') ? '/gestion' : '/');
            });
    }

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
                ->description("Permissions accordées à cet utilisateur en plus de celles de ses rôles, regroupées par catégorie.")
                ->schema(PermissionField::groupedSchema()),
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
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rôle')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                static::configureImpersonateAction(Tables\Actions\Action::make('impersonate')),
                Tables\Actions\EditAction::make(),
            ])
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
