<?php

namespace App\Filament\Resources\GroupProgrammeResource\Pages;

use App\Filament\Resources\GroupProgrammeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroupProgramme extends EditRecord
{
    protected static string $resource = GroupProgrammeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
