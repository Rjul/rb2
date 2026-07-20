<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Introduction de la vérification d'email (OTP) : on considère les comptes
 * DÉJÀ existants comme vérifiés pour ne locker aucun utilisateur réel.
 * La vérification OTP + le tri « email non vérifié » ne concernent donc que
 * les inscriptions à partir de maintenant (là où arrivent les bots).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Irréversible : on ne « dé-vérifie » pas des comptes (risquerait de locker des vrais users).
    }
};
