<?php

namespace App\Filament\Support;

use App\Models\Attachment;
use App\Models\Emision;
use Illuminate\Support\Facades\Storage;

/**
 * Fait le pont entre l'upload Filament (un fichier sur un disque) et le système
 * d'Attachment d'Orchid attendu par le front (`$emision->attachment->first()->url`).
 * Le média audio est stocké sur `emission_audio` (local), la vidéo sur `emission_video` (FTP).
 */
class EmisionMedia
{
    public static function disk(string $mediaType): string
    {
        return $mediaType === Emision::TYPE_VIDEO ? 'emission_video' : 'emission_audio';
    }

    /**
     * Chemin physique du média courant (pour préremplir le champ à l'édition).
     */
    public static function currentPath(Emision $emision): ?string
    {
        if (! in_array($emision->media_type, [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO], true)) {
            return null;
        }

        $attachment = $emision->attachment($emision->media_type)->first();

        return $attachment ? $attachment->path . $attachment->name . '.' . $attachment->extension : null;
    }

    /**
     * Crée l'Attachment à partir du fichier uploadé et l'associe à l'émission.
     * Idempotent : ne recrée rien si le média est déjà attaché.
     */
    public static function sync(Emision $emision, ?string $path): void
    {
        if (blank($path) || ! in_array($emision->media_type, [Emision::TYPE_AUDIO, Emision::TYPE_VIDEO], true)) {
            return;
        }

        $disk = static::disk($emision->media_type);

        $filename = basename($path);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);

        $dir = ltrim(str_replace('\\', '/', dirname($path)), '.');
        $dir = ($dir === '' || $dir === '/') ? '' : rtrim($dir, '/') . '/';

        // Déjà attaché à l'identique -> rien à faire.
        $physical = $dir . $name . '.' . $extension;
        $already = $emision->attachment()->get()
            ->first(fn (Attachment $a) => ($a->path . $a->name . '.' . $a->extension) === $physical);
        if ($already) {
            return;
        }

        $attachment = Attachment::create([
            'name'          => $name,
            'original_name' => $filename,
            'mime'          => rescue(fn () => Storage::disk($disk)->mimeType($path), 'application/octet-stream', false),
            'extension'     => $extension,
            'size'          => rescue(fn () => Storage::disk($disk)->size($path), 0, false),
            'path'          => $dir,
            'disk'          => $disk,
            'group'         => $emision->media_type,
            'sort'          => 0,
            'user_id'       => auth()->id(),
        ]);

        // Une émission n'a qu'un média : on remplace l'existant.
        $emision->attachment()->sync([$attachment->id]);
    }
}
