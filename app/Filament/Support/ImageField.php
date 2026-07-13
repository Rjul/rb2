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
            ->helperText("Format attendu : {$width}×{$height} px (recadrage imposé).");
    }
}
