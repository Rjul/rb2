<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmisionResource\Pages;
use App\Models\Emision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmisionResource extends Resource
{
    protected static ?string $model = Emision::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationGroup = 'Contenu';
    protected static ?string $navigationLabel = 'Émissions';
    protected static ?string $modelLabel = 'émission';
    protected static ?string $pluralModelLabel = 'émissions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Titre')->required()->maxLength(255)->columnSpanFull(),

                Forms\Components\Select::make('media_type')
                    ->label('Type de média')
                    ->options([
                        Emision::TYPE_TEXT => 'Article (texte)',
                        Emision::TYPE_AUDIO => 'Audio',
                        Emision::TYPE_VIDEO => 'Vidéo',
                    ])
                    ->default(Emision::TYPE_AUDIO)
                    ->required()->live(),

                Forms\Components\Select::make('programme_id')
                    ->label('Programme')
                    ->relationship('programme', 'name')
                    ->searchable()->preload()->required(),

                Forms\Components\Select::make('tags')
                    ->label('Thèmes associés')
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->preload(),

                Forms\Components\DatePicker::make('active_at')
                    ->label('Date de publication')->required(),

                Forms\Components\TextInput::make('duration')
                    ->label('Durée / temps de consultation (min)')
                    ->numeric()->minValue(0)->maxValue(120)->step(0.01)
                    ->visible(fn (Get $get) => in_array($get('media_type'), [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO]))
                    ->required(fn (Get $get) => in_array($get('media_type'), [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO])),

                Forms\Components\Toggle::make('is_active')->label('Visible')->default(true),
                Forms\Components\Toggle::make('is_put_forward')->label('Mettre à la une')->default(false),

                Forms\Components\FileUpload::make('media_upload')
                    ->label(fn (Get $get) => $get('media_type') === Emision::TYPE_VIDEO ? 'Fichier vidéo' : 'Fichier audio')
                    ->disk(fn (Get $get) => \App\Filament\Support\EmisionMedia::disk($get('media_type') ?? Emision::TYPE_AUDIO))
                    ->visibility('public')
                    ->directory(date('Y/m'))
                    ->acceptedFileTypes(fn (Get $get) => $get('media_type') === Emision::TYPE_VIDEO ? ['video/mp4', 'video/*'] : ['audio/mpeg', 'audio/*'])
                    ->maxSize(512000)
                    ->helperText("Fichier enregistré comme média de l'émission (audio local, vidéo sur le FTP).")
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('media_type'), [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO])),
            ]),

            \App\Filament\Support\ImageField::make(800, 533, 'images')
                ->required()->columnSpanFull(),

            Forms\Components\RichEditor::make('description')
                ->label('Description')->required()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Titre')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('programme.name')->label('Programme')->sortable(),
                Tables\Columns\TextColumn::make('media_type')->label('Type')->badge()
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->color(fn (string $state) => match ($state) {
                        Emision::TYPE_AUDIO => 'info',
                        Emision::TYPE_VIDEO => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('active_at')->label('Publication')->date('d/m/Y')->sortable(),
            ])
            ->defaultSort('active_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('programme')->relationship('programme', 'name')->label('Programme'),
                Tables\Filters\SelectFilter::make('media_type')->label('Type')->options([
                    Emision::TYPE_TEXT => 'Texte',
                    Emision::TYPE_AUDIO => 'Audio',
                    Emision::TYPE_VIDEO => 'Vidéo',
                ]),
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
            'index' => Pages\ListEmisions::route('/'),
            'create' => Pages\CreateEmision::route('/create'),
            'edit' => Pages\EditEmision::route('/{record}/edit'),
        ];
    }
}
