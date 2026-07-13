<?php

namespace App\Filament\Resources\WebsiteNewResource\Pages;

use App\Filament\Resources\WebsiteNewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteNew extends EditRecord
{
    protected static string $resource = WebsiteNewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
