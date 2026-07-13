<?php

namespace App\Filament\Resources\PageAdminResource\Pages;

use App\Filament\Resources\PageAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageAdmin extends EditRecord
{
    protected static string $resource = PageAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
