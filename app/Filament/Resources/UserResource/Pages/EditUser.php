<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Support\ManagesGroupedPermissions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use ManagesGroupedPermissions;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            UserResource::configureImpersonateAction(Actions\Action::make('impersonate')),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->hydratePermissionGroups($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->collectPermissionGroups($data);
    }
}
