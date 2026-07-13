<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

/**
 * Équivalent Filament du Cropper Orchid : recadrage IMPOSÉ au ratio attendu
 * (crop côté client à l'upload + éditeur de recadrage), dimensions de sortie fixes,
 * sur le disque public `emission_image`.
 * L'accessor HasResolvedImage renvoie ensuite une URL affichable côté front.
 */
class ImageField
{
    public static function make(int $width, int $height, string $directory = 'images'): FileUpload
    {
        $ratio = $width . ':' . $height;

        return FileUpload::make('image')
            ->label('Image')
            ->image()
            ->disk('emission_image')
            ->visibility('public')
            ->directory($directory)
            // Recadrage imposé (comme le Cropper Orchid) :
            ->imageCropAspectRatio($ratio)          // force le crop au ratio à l'upload
            ->imageResizeMode('cover')
            ->imageResizeTargetWidth((string) $width)
            ->imageResizeTargetHeight((string) $height)
            // Éditeur de recadrage manuel, verrouillé sur le même ratio :
            ->imageEditor()
            ->imageEditorAspectRatios([$ratio])
            ->helperText("Format imposé : {$width}×{$height} px.");
    }
}
