<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Site';
    protected static ?string $navigationLabel = 'Commentaires';
    protected static ?string $modelLabel = 'commentaire';
    protected static ?string $pluralModelLabel = 'commentaires';

    // Modération uniquement : pas de création manuelle.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('approved', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('comment')
                ->label('Commentaire')->disabled()->rows(4)->columnSpanFull(),
            Forms\Components\Toggle::make('approved')->label('Approuvé'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('commentable.name')
                    ->label('Émission')
                    ->getStateUsing(fn (Comment $record) => $record->commentable?->name ?? '—'),
                Tables\Columns\TextColumn::make('programme')
                    ->label('Programme')
                    ->getStateUsing(fn (Comment $record) => $record->commentable?->programme?->name ?? '—'),
                Tables\Columns\TextColumn::make('comment')->label('Commentaire')->wrap()->limit(120),
                Tables\Columns\TextColumn::make('commenter_name')
                    ->label('Auteur')
                    ->getStateUsing(fn (Comment $record) => $record->commenter?->name ?? $record->guest_name ?? 'Invité'),
                Tables\Columns\IconColumn::make('approved')->label('Approuvé')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('approved', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('approved')->label('Approuvé'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleApproved')
                    ->label(fn (Comment $record) => $record->approved ? 'Désapprouver' : 'Approuver')
                    ->icon(fn (Comment $record) => $record->approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Comment $record) => $record->approved ? 'danger' : 'success')
                    ->action(fn (Comment $record) => $record->update(['approved' => ! $record->approved])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approuver')
                        ->icon('heroicon-o-check-circle')->color('success')
                        ->action(fn ($records) => $records->each->update(['approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
