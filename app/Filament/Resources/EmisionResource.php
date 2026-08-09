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
            // Le badge « Statut » lit le programme de chaque ligne → eager-load (anti N+1).
            ->with('programme')
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
                    ->relationship('tags', 'name', fn (Builder $query) => $query->orderByName())
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    // Recherche insensible à la casse sur le nom traduit (JSON) :
                    // le LIKE par défaut compare le JSON en binaire sur MySQL.
                    ->getSearchResultsUsing(fn (string $search) => \App\Models\Tag::containing($search)
                        ->orderByName()
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($tag) => [$tag->id => (string) $tag->name])
                        ->all())
                    ->preload()->required(),

                Forms\Components\DatePicker::make('active_at')
                    ->label('Date de publication')
                    // native(false) : le calendrier s'ouvre au clic n'importe où dans le champ
                    ->native(false)->displayFormat('d/m/Y')->closeOnDateSelection()
                    ->default(now())->required(),

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
                // Statut réel côté public (règle unique : Emision::isPublished()).
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->state(fn (Emision $record) => match (true) {
                        ! $record->is_active => 'Brouillon',
                        ! $record->programme?->is_active => 'Programme inactif',
                        $record->active_at && \Illuminate\Support\Carbon::parse($record->active_at)->isFuture() => 'Programmée',
                        default => 'Publiée',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Publiée'           => 'success',
                        'Programmée'        => 'info',
                        'Brouillon'         => 'gray',
                        'Programme inactif' => 'danger',
                        default             => 'gray',
                    }),
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
                // Filtre par statut public (remplace l'ancien ternaire « Active »).
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'publiee'           => 'Publiée',
                        'programmee'        => 'Programmée',
                        'brouillon'         => 'Brouillon',
                        'programme_inactif' => 'Programme inactif',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'publiee' => $query->where('is_active', true)->where('active_at', '<=', now())
                            ->whereHas('programme', fn ($q) => $q->where('is_active', true)),
                        'programmee' => $query->where('is_active', true)->where('active_at', '>', now()),
                        'brouillon'  => $query->where('is_active', false),
                        'programme_inactif' => $query->where('is_active', true)
                            ->whereHas('programme', fn ($q) => $q->where('is_active', false)),
                        default => $query,
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('voir')
                    // Non publiée → le lien ouvre la fiche en mode préversion (réservé BO).
                    ->label(fn (Emision $record) => $record->isPublished() ? 'Voir sur le site' : 'Prévisualiser')
                    ->icon(fn (Emision $record) => $record->isPublished() ? 'heroicon-o-arrow-top-right-on-square' : 'heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Emision $record) => $record->canonicalUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (Emision $record) => $record->programme !== null && filled($record->slug)),
                // Duplication (émissions récurrentes) : copie éditoriale en BROUILLON —
                // titre/programme/description/image/thèmes repris, slug régénéré (name+id),
                // fichiers audio/vidéo NON copiés (chaque numéro a son propre média).
                Tables\Actions\Action::make('dupliquer')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Emision $record) {
                        $copy = $record->replicate(['slug']);
                        $copy->name           = $record->name . ' (copie)';
                        $copy->is_active      = false;
                        $copy->is_put_forward = false;
                        $copy->active_at      = now();
                        $copy->save();

                        // Régénère le slug maintenant que l'id existe (name + id, unique),
                        // comme à la création (CreateEmision::afterCreate).
                        $copy->generateSlug();
                        $copy->save();

                        $copy->tags()->sync($record->tags()->pluck('tags.id'));

                        return redirect(static::getUrl('edit', ['record' => $copy]));
                    }),
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
