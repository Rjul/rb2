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
        // Préremplit le champ d'upload avec le média courant.
        $data['media_upload'] = EmisionMedia::currentPath($this->record);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->mediaPath = $data['media_upload'] ?? null;
        unset($data['media_upload']);

        return $data;
    }

    protected function afterSave(): void
    {
        EmisionMedia::sync($this->record, $this->mediaPath);
    }
}
