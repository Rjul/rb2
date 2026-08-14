<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Répare les attachments migrés de l'ancienne base (2020, path `old/…`) :
 * la commande migrate:old_db stockait l'extension DANS `name` (« 90.mp3 »)
 * et laissait la colonne `extension` vide. Or tous les constructeurs d'URL
 * (Emision::audioUrl, RSS, Orchid physicalPath) font `name . '.' . extension`
 * → « old/0/90.mp3. » (point final) → 404 sur tous les vieux audios/images.
 *
 * Une ligne n'est scindée QUE si le fichier physique attendu (« old/0/90.mp3 »)
 * existe réellement sur son disque. Les autres restent intactes et sont
 * consignées dans un RAPPORT (storage/app/) : total réparé / échecs + détail.
 * Idempotente et relançable (les lignes réparées ne matchent plus le filtre,
 * les échecs restent listés à chaque passage).
 */
return new class extends Migration
{
    /** Chemin du rapport (surchargable par les tests) ; défaut : storage/app/. */
    public ?string $reportPath = null;

    public function up(): void
    {
        $fixed    = 0;
        $failures = [];

        DB::table('attachments')
            ->where(fn ($q) => $q->whereNull('extension')->orWhere('extension', ''))
            ->where('name', 'like', '%.%')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$fixed, &$failures) {
                foreach ($rows as $row) {
                    $ext  = pathinfo($row->name, PATHINFO_EXTENSION);
                    $base = pathinfo($row->name, PATHINFO_FILENAME);

                    // Garde-fou : une vraie extension courte (mp3, jpg, jpeg, png…)
                    // et un nom restant non vide, sinon la ligne n'est pas concernée.
                    if ($base === '' || ! preg_match('/^[a-zA-Z0-9]{2,4}$/', $ext)) {
                        continue;
                    }

                    $file = trim((string) $row->path, '/') . '/' . $base . '.' . strtolower($ext);

                    // Le fichier physique doit exister sur le disque de la ligne
                    // (rescue : un disque distant injoignable = échec consigné, pas un crash).
                    $exists = rescue(fn () => Storage::disk($row->disk)->exists($file), false, false);

                    if (! $exists) {
                        $failures[] = sprintf('#%d  disk=%s  attendu=%s  (name=%s)', $row->id, $row->disk, $file, $row->name);

                        continue; // ligne laissée intacte → re-listée au prochain passage
                    }

                    DB::table('attachments')->where('id', $row->id)->update([
                        'name'      => $base,
                        'extension' => strtolower($ext),
                    ]);
                    $fixed++;
                }
            });

        $this->writeReport($fixed, $failures);
    }

    public function down(): void
    {
        // Réparation de données : pas de retour arrière automatique (l'état
        // antérieur était un défaut de migration, pas un état à restaurer).
    }

    /** @param list<string> $failures */
    private function writeReport(int $fixed, array $failures): void
    {
        $path = $this->reportPath
            ?? storage_path('app/rapport-fix-extensions-attachments-' . now()->format('Ymd-His') . '.txt');

        $lines = [
            'Réparation des extensions d\'attachments (migration 2026_08_09_110000)',
            'Exécutée le : ' . now()->format('d/m/Y H:i:s'),
            '',
            'Réparés (fichier physique vérifié) : ' . $fixed,
            'Échecs (fichier introuvable, ligne laissée intacte) : ' . count($failures),
            '',
        ];

        $lines[] = $failures === []
            ? 'Aucun échec.'
            : "Détail des échecs :\n" . implode("\n", $failures);

        @mkdir(dirname($path), 0775, true);
        file_put_contents($path, implode("\n", $lines) . "\n");

        $summary = sprintf('Attachments : %d réparé(s), %d échec(s) — rapport : %s', $fixed, count($failures), $path);
        Log::info($summary);
        echo "\n  {$summary}\n";
    }
};
