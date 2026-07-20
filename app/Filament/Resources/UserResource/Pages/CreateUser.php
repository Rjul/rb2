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

    /**
     * Utilisateur créé par un admin → email considéré vérifié directement
     * (pas d'OTP). On le fait après création : email_verified_at n'est pas
     * dans $fillable, donc l'assignation de masse l'ignorerait.
     */
    protected function afterCreate(): void
    {
        if (! $this->record->hasVerifiedEmail()) {
            $this->record->forceFill(['email_verified_at' => now()])->save();
        }
    }
}
