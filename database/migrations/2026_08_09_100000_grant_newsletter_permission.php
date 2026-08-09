<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * La ressource « Newsletter » (Filament) a désormais sa permission dédiée
 * `platform.newsletter` (avant : elle empruntait `platform.annonces`).
 * Continuité : tout rôle/utilisateur qui possédait « Annonces » (et voyait donc
 * la newsletter) reçoit la nouvelle permission. Les nouveaux droits se gèrent
 * ensuite dans l'UI des rôles (case « Newsletter (abonnés) »).
 */
return new class extends Migration
{
    private const TABLES = ['roles', 'users'];

    public function up(): void
    {
        $this->eachPermissionRow(function (array $perms) {
            if (! empty($perms['platform.annonces']) && ! isset($perms['platform.newsletter'])) {
                $perms['platform.newsletter'] = true;

                return $perms;
            }

            return null; // pas de changement
        });
    }

    public function down(): void
    {
        $this->eachPermissionRow(function (array $perms) {
            if (array_key_exists('platform.newsletter', $perms)) {
                unset($perms['platform.newsletter']);

                return $perms;
            }

            return null;
        });
    }

    /** Applique $transform au JSON `permissions` de chaque ligne (null = inchangé). */
    private function eachPermissionRow(callable $transform): void
    {
        foreach (self::TABLES as $table) {
            DB::table($table)->whereNotNull('permissions')->orderBy('id')
                ->chunkById(100, function ($rows) use ($table, $transform) {
                    foreach ($rows as $row) {
                        $perms = json_decode($row->permissions, true);
                        if (! is_array($perms)) {
                            continue;
                        }
                        if (($updated = $transform($perms)) !== null) {
                            DB::table($table)->where('id', $row->id)
                                ->update(['permissions' => json_encode($updated)]);
                        }
                    }
                });
        }
    }
};
