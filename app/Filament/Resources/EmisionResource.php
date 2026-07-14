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
use Illuminate\Database\Eloquent\Builder;

class EmisionResource extends Resource
{
    protected static ?string $model = Emision::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationGroup = 'Contenu';
    protected static ?string $navigationLabel = 'Émissions';
    protected static ?string $modelLabel = 'émission';
    protected static ?string $pluralModelLabel = 'émissions';

    /**
     * Restreint la liste aux émissions des programmes autorisés (comme Orchid).
     * `platform.programmes` = tout ; sinon uniquement les `platform.emission.{id}`
     * accordés (permissions directes ∪ rôles). Le groupe `where()` évite que les
     * `orWhere` du scope ne cassent la recherche/les filtres de Filament.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(fn (Builder $query) => $query->withAuthPermissions());
    }

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
                    ->relationship('programme', 'name', fn (Builder $query) => $query->where(fn (Builder $q) => $q->withAuthPermissions()))
                    ->searchable()->preload()->required(),

                Forms\Components\Select::make('tags')
                    ->label('Thèmes associés')
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->preload()->required(),

                Forms\Components\DatePicker::make('active_at')
                    ->label('Date de publication')->required(),

                Forms\Components\TextInput::make('duration')
                    ->label('Durée / temps de consultation (min)')
                    ->numeric()->minValue(0)->maxValue(120)->step(0.01)
                    ->visible(fn (Get $get) => in_array($get('media_type'), [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO]))
                    ->required(fn (Get $get) => in_array($get('media_type'), [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO])),

                Forms\Components\Toggle::make('is_active')->label('Visible')->default(true),
                Forms\Components\Toggle::make('is_put_forward')->label('Mettre à la une')->default(true),

                Forms\Components\FileUpload::make('audio_upload')
                    ->label('Fichier audio')
                    ->disk('emission_audio')->visibility('public')->directory(date('Y/m'))
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/*'])
                    ->maxSize(512000)
                    ->helperText("Fichier audio de l'émission.")
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('media_type') === Emision::TYPE_AUDIO),

                Forms\Components\FileUpload::make('video_upload')
                    ->label('Fichier vidéo')
                    ->disk('emission_video')->visibility('public')->directory(date('Y/m'))
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/webm', 'video/*'])
                    ->maxSize(2048000)
                    ->helperText('Fichier vidéo (stocké sur le FTP).')
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('media_type') === Emision::TYPE_VIDEO),
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
            ->actions([
                Tables\Actions\Action::make('voir')
                    ->label('Voir sur le site')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Emision $record) => route('view-emision', ['programme' => $record->programme, 'emision' => $record]))
                    ->openUrlInNewTab()
                    ->visible(fn (Emision $record) => $record->programme !== null && filled($record->slug)),
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
            'index' => Pages\ListEmisions::route('/'),
            'create' => Pages\CreateEmision::route('/create'),
            'edit' => Pages\EditEmision::route('/{record}/edit'),
        ];
    }
}
