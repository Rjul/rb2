<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgrammeResource\Pages;
use App\Models\Programme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgrammeResource extends Resource
{
    protected static ?string $model = Programme::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Contenu';
    protected static ?string $navigationLabel = 'Programmes';
    protected static ?string $modelLabel = 'programme';
    protected static ?string $pluralModelLabel = 'programmes';

    /**
     * Restreint la liste aux programmes autorisés (comme Orchid) :
     * `platform.programmes` = tout, sinon uniquement les `platform.emission.{id}`.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(fn (Builder $query) => $query->withAuthPermissions());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->columnSpanFull()->tabs([
                Forms\Components\Tabs\Tab::make('Général')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Titre')->required()->maxLength(255),
                    Forms\Components\TextInput::make('height')
                        ->label('Priorité de tri')->numeric()->required(),
                    Forms\Components\Select::make('group_programme_id')
                        ->label('Groupe du programme')
                        ->relationship('group_programme', 'name')
                        ->searchable()->preload()->required(),
                    Forms\Components\RichEditor::make('description')
                        ->label('Description')->required()->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Programme visible')->default(true),
                    Forms\Components\Toggle::make('is_archived')
                        ->label('Archiver le programme')->default(false),
                ]),
                Forms\Components\Tabs\Tab::make('RSS')->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Administrateur du programme')
                        ->relationship('user', 'name')
                        ->searchable()->preload(),
                    \App\Filament\Support\ImageField::make(800, 533, 'programmes'),
                    Forms\Components\Toggle::make('has_rss')
                        ->label('Activer le flux RSS')->default(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('group_programme.name')->label('Groupe')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_archived')->label('Archivé')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('has_rss')->label('Flux RSS')->boolean()->sortable(),
            ])
            ->defaultSort('id')
            ->filters([
                Tables\Filters\SelectFilter::make('group_programme')->relationship('group_programme', 'name')->label('Groupe'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index' => Pages\ListProgrammes::route('/'),
            'create' => Pages\CreateProgramme::route('/create'),
            'edit' => Pages\EditProgramme::route('/{record}/edit'),
        ];
    }
}
