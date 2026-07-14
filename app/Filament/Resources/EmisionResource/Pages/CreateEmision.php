<?php

namespace App\Filament\Resources\EmisionResource\Pages;

use App\Filament\Resources\EmisionResource;
use App\Filament\Support\EmisionMedia;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateEmision extends CreateRecord
{
    protected static string $resource = EmisionResource::class;

    protected ?string $mediaPath = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Le fichier média n'est pas une colonne : on l'extrait pour créer l'Attachment après.
        $this->mediaPath = $data['audio_upload'] ?? $data['video_upload'] ?? null;
        unset($data['audio_upload'], $data['video_upload']);

        // Reprend le comportement Orchid : l'auteur = utilisateur courant.
        $data['user_id'] ??= Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        // Régénère le slug maintenant que l'id existe (source = name + id, comme Orchid),
        // pour garantir un slug unique même si deux émissions portent le même titre.
        $this->record->generateSlug();
        $this->record->save();

        EmisionMedia::sync($this->record, $this->mediaPath);
    }
}
