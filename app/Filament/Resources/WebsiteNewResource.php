<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebsiteNewResource\Pages;
use App\Models\WebsiteNew;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebsiteNewResource extends Resource
{
    protected static ?string $model = WebsiteNew::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Site';
    protected static ?string $navigationLabel = 'Annonces';
    protected static ?string $modelLabel = 'annonce';
    protected static ?string $pluralModelLabel = 'annonces';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Titre')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('content')
                ->label('Texte à afficher')
                ->required()
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('active')
                ->label('Annonce visible')
                ->default(true),
            Forms\Components\DateTimePicker::make('active_at')
                ->label('Date de publication')
                ->seconds(false),
            Forms\Components\DateTimePicker::make('end_at')
                ->label('Date de fin')
                ->seconds(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('active')->label('Active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('active_at')->label('Publication')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('end_at')->label('Fin')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('active_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('active')->label('Active'),
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
            'index' => Pages\ListWebsiteNews::route('/'),
            'create' => Pages\CreateWebsiteNew::route('/create'),
            'edit' => Pages\EditWebsiteNew::route('/{record}/edit'),
        ];
    }
}
