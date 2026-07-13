<?php

namespace App\Filament\Resources\WebsiteNewResource\Pages;

use App\Filament\Resources\WebsiteNewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteNews extends ListRecords
{
    protected static string $resource = WebsiteNewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
