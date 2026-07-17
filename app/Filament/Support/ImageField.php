<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
            // À l'édition, le formulaire est rempli via l'accessor HasResolvedImage
            // (une URL), alors que FileUpload attend un chemin RELATIF au disque :
            // exists() échoue et l'aperçu reste vide. On repart de la colonne brute
            // et on retrouve le chemin disque pour les formats hérités (URL absolue
            // du Cropper Orchid, chemin complet d'avant 2023-11).
            ->afterStateHydrated(static function (FileUpload $component, ?Model $record): void {
                if (! $record) {
                    return; // création : état vide par défaut
                }

                $raw = $record->getRawOriginal('image');

                if (blank($raw)) {
                    $component->state([]);

                    return;
                }

                $path = $raw;

                if (preg_match('~storage/public/emission/images/(.+)$~', $raw, $matches)) {
                    $path = $matches[1];
                }

                // Introuvable sur le disque (ex. URL externe) : on conserve la
                // valeur d'origine — pas d'aperçu, mais rien n'est perdu à la
                // sauvegarde et le champ requis reste satisfait.
                $component->state(
                    Storage::disk('emission_image')->exists($path) ? [$path] : [$raw]
                );
            })
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
