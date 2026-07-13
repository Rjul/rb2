<?php

namespace App\Filament\Resources\PageAdminResource\Pages;

use App\Filament\Resources\PageAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageAdmins extends ListRecords
{
    protected static string $resource = PageAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
