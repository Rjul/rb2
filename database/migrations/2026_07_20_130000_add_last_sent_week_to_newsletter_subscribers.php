<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mémorise la semaine ISO où l'abonné a reçu la dernière newsletter.
 * Permet l'envoi par vagues sur plusieurs jours sans jamais envoyer deux fois
 * la même édition à la même personne.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->string('last_sent_week')->nullable()->index()->after('unsubscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropColumn('last_sent_week');
        });
    }
};
