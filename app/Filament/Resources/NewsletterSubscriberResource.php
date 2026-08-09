<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Filament\Support\GatesAccessByPermission;
use App\Models\NewsletterSubscriber;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    use GatesAccessByPermission;

    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Site';
    protected static ?string $navigationLabel = 'Newsletter';
    protected static ?string $modelLabel = 'abonné newsletter';
    protected static ?string $pluralModelLabel = 'abonnés newsletter';

    protected static function permissionSlug(): ?string
    {
        // Permission dédiée (déclarée dans PlatformProvider::registerPermissions,
        // donc cochable dans l'UI des rôles) — n'emprunte plus « Annonces ».
        return 'platform.newsletter';
    }

    // Les abonnés viennent du formulaire public (double opt-in), pas d'ajout manuel.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('verified_at')->label('Confirmé')->boolean()
                    ->getStateUsing(fn (NewsletterSubscriber $r) => $r->verified_at !== null),
                Tables\Columns\IconColumn::make('unsubscribed_at')->label('Désinscrit')->boolean()
                    ->getStateUsing(fn (NewsletterSubscriber $r) => $r->unsubscribed_at !== null),
                Tables\Columns\TextColumn::make('created_at')->label('Inscrit le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                // Ménage : isoler les emails non confirmés (bots / inscriptions abandonnées).
                Tables\Filters\TernaryFilter::make('verified_at')
                    ->label('Confirmé (double opt-in)')
                    ->nullable()
                    ->placeholder('Tous')
                    ->trueLabel('Confirmés uniquement')
                    ->falseLabel('Non confirmés (à nettoyer)'),
                Tables\Filters\TernaryFilter::make('unsubscribed_at')
                    ->label('Désinscrit')
                    ->nullable()
                    ->placeholder('Tous')
                    ->trueLabel('Désinscrits')
                    ->falseLabel('Actifs'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListNewsletterSubscribers::route('/'),
        ];
    }
}
