<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

/**
 * Équivalent Filament du Cropper Orchid : recadrage au format attendu
 * (ratio + dimensions de sortie fixes) sur le disque public `emission_image`.
 * L'accessor HasResolvedImage renvoie ensuite une URL affichable côté front.
 */
class ImageField
{
    public static function make(int $width, int $height, string $directory = 'images'): FileUpload
    {
        return FileUpload::make('image')
            ->label('Image')
            ->image()
            ->disk('emission_image')
            ->visibility('public')
            ->directory($directory)
            ->imageEditor()
            ->imageEditorAspectRatios([$width . ':' . $height])
            ->imageResizeMode('cover')
            ->imageResizeTargetWidth((string) $width)
            ->imageResizeTargetHeight((string) $height)
            ->helperText("Format attendu : {$width}×{$height} px (recadrage imposé).")
            ->formatStateUsing(fn ($state) => static::urlToPath($state));
    }

    /**
     * Reconvertit l'URL (renvoyée par l'accessor) en chemin relatif au disque
     * pour que l'aperçu de FileUpload retrouve le fichier.
     */
    public static function urlToPath($state)
    {
        if (blank($state)) {
            return $state;
        }

        $base = Storage::disk('emission_image')->url('');

        if (str_starts_with($state, $base)) {
            return ltrim(substr($state, strlen($base)), '/');
        }

        // URL absolue d'un autre disque (héritée d'Orchid) : pas d'aperçu, mais pas d'erreur.
        return str_starts_with($state, 'http') ? null : $state;
    }
}
