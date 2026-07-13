<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Résout la colonne `image` en URL affichable.
 * - Orchid (Cropper ->targetUrl()) stocke une URL absolue -> renvoyée telle quelle.
 * - Filament (FileUpload) stocke un chemin relatif sur le disque public `emission_image`
 *   -> converti en URL.
 * Permet la coexistence Orchid/Filament sans casser l'affichage front (`{{ $model->image }}`).
 */
trait HasResolvedImage
{
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return Storage::disk('emission_image')->url($value);
    }
}
