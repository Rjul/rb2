<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Support\ManagesGroupedPermissions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use ManagesGroupedPermissions;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->collectPermissionGroups($data);
    }
}
