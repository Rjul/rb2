<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupProgrammeResource\Pages;
use App\Filament\Support\GatesAccessByPermission;
use App\Models\GroupProgramme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupProgrammeResource extends Resource
{
    use GatesAccessByPermission;

    protected static ?string $model = GroupProgramme::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Contenu';
    protected static ?string $navigationLabel = 'Groupes de programme';
    protected static ?string $modelLabel = 'groupe de programme';
    protected static ?string $pluralModelLabel = 'groupes de programme';

    protected static function permissionSlug(): ?string
    {
        return 'platform.group.programme';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Titre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('height')
                ->label('Priorité de tri')
                ->numeric()
                ->required(),
            Forms\Components\RichEditor::make('description')
                ->label('Description')
                ->required()
                ->columnSpanFull(),
            \App\Filament\Support\ImageField::make(533, 800, 'groups'),
            Forms\Components\Toggle::make('is_active')
                ->label('Groupe visible')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('height')->label('Tri')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
            ])
            ->defaultSort('height')
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
            'index' => Pages\ListGroupProgrammes::route('/'),
            'create' => Pages\CreateGroupProgramme::route('/create'),
            'edit' => Pages\EditGroupProgramme::route('/{record}/edit'),
        ];
    }
}
