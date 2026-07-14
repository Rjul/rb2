<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageAdminResource\Pages;
use App\Filament\Support\GatesAccessByPermission;
use App\Models\PageAdmin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageAdminResource extends Resource
{
    use GatesAccessByPermission;

    protected static ?string $model = PageAdmin::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Contenu';
    protected static ?string $navigationLabel = 'Pages';
    protected static ?string $modelLabel = 'page';
    protected static ?string $pluralModelLabel = 'pages';

    protected static function permissionSlug(): ?string
    {
        return 'platform.page-admin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nom')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('path')
                ->label("Route d'accès")
                ->helperText('URL publique de la page (doit être unique).')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\RichEditor::make('content')
                ->label('Contenu')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('path')->label('URL')->searchable()->sortable(),
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
            'index' => Pages\ListPageAdmins::route('/'),
            'create' => Pages\CreatePageAdmin::route('/create'),
            'edit' => Pages\EditPageAdmin::route('/{record}/edit'),
        ];
    }
}
