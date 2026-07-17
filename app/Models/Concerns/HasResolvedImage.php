<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Résout la colonne `image` en URL affichable, en gérant les 3 formats coexistants :
 *
 *  1. URL absolue            "https://…"                          → renvoyée telle quelle (ancien Orchid Cropper).
 *  2. Chemin déjà complet    "storage/public/emission/images/old/…" ou "/storage/…"
 *                            → servi tel quel depuis la racine web (images d'AVANT 2023-11-27,
 *                              rangées sous old/… : la valeur contient déjà tout le chemin).
 *  3. Chemin relatif disque  "2023/11/30/…png", "images/…jpg"     → préfixé par l'URL du disque public.
 *
 * Sans le cas (2), le trait re-préfixait ces vieilles valeurs avec l'URL du disque, produisant
 * un doublon "…/emission/images/storage/public/emission/images/old/…" → 404.
 */
trait HasResolvedImage
{
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        // (1) URL absolue → telle quelle.
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // (2) Chemin déjà complet sous la racine web (ancien format) → ne pas re-préfixer.
        if (str_starts_with($value, '/') || str_starts_with($value, 'storage/')) {
            return url($value);
        }

        // (3) Nouveau format Filament : chemin relatif au disque public `emission_image`.
        return Storage::disk('emission_image')->url($value);
    }
}
