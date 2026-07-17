<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;

/**
 * Équivalent Filament du Cropper Orchid : recadrage IMPOSÉ au ratio attendu
 * (crop côté client à l'upload + éditeur de recadrage), dimensions de sortie fixes,
 * sur le disque public `emission_image`. Utilisé pour les images d'émission, de
 * programme et de catégorie (groupe de programme).
 *
 * Édition d'un enregistrement existant — les valeurs héritées coexistent en 3 formats :
 *   1. chemin relatif au disque   "programmes/xxx.jpg"           (nouveaux uploads Filament)
 *   2. chemin complet racine web  "storage/public/emission/images/old/…jpg"  (avant 2023-11)
 *   3. URL absolue                "https://…/storage/public/…"   (Cropper Orchid `targetUrl()`)
 *
 * FileUpload attend un chemin relatif à SON disque et vérifie son existence ;
 * pour ces formats hérités (voire un fichier posé par Orchid sur le disque `public`,
 * hors `emission_image`), la vérif échoue et l'aperçu disparaît — alors que le
 * fichier existe et s'affiche sur le site. On résout donc l'aperçu via l'accessor
 * du modèle (HasResolvedImage), seule source de vérité pour transformer n'importe
 * quel format en URL affichable, et on garde la valeur brute pour ne rien perdre
 * à l'enregistrement.
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
            // On place la valeur BRUTE en état : elle sert de clé d'aperçu (résolue
            // en URL ci-dessous) et, si l'image n'est pas remplacée, elle est
            // ré-enregistrée telle quelle → aucune perte ni reformatage hasardeux.
            ->afterStateHydrated(static function (FileUpload $component, ?Model $record): void {
                $raw = $record?->getRawOriginal('image');
                $component->state(blank($raw) ? [] : [(string) $raw]);
            })
            // Pas de vérif d'existence sur le disque du champ : les valeurs héritées
            // peuvent être des URL absolues ou vivre sur le disque Orchid `public`.
            ->fetchFileInformation(false)
            // Aperçu : l'accessor du modèle sait rendre les 3 formats en URL.
            ->getUploadedFileUsing(static function (FileUpload $component, string $file): ?array {
                $record = $component->getRecord();
                $url = $record?->image ?: $file;

                return [
                    'name' => basename(parse_url($file, PHP_URL_PATH) ?: $file),
                    'url'  => $url,
                ];
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
