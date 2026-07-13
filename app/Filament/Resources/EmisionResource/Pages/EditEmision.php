<?php

namespace App\Filament\Resources\EmisionResource\Pages;

use App\Filament\Resources\EmisionResource;
use App\Filament\Support\EmisionMedia;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmision extends EditRecord
{
    protected static string $resource = EmisionResource::class;

    protected ?string $mediaPath = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Préremplit le bon champ d'upload avec le média courant.
        $path = EmisionMedia::currentPath($this->record);

        if ($this->record->media_type === \App\Models\Emision::TYPE_VIDEO) {
            $data['video_upload'] = $path;
        } else {
            $data['audio_upload'] = $path;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->mediaPath = $data['audio_upload'] ?? $data['video_upload'] ?? null;
        unset($data['audio_upload'], $data['video_upload']);

        return $data;
    }

    protected function afterSave(): void
    {
        EmisionMedia::sync($this->record, $this->mediaPath);
    }
}
