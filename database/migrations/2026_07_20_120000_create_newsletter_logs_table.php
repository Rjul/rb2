<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal des envois de la newsletter : une ligne par JOUR d'envoi (vague).
 * Garantit une seule vague par jour même si le cron OVH appelle la commande
 * toutes les heures (contrainte unique sur `date`). L'envoi hebdomadaire peut
 * ainsi s'étaler sur plusieurs jours (vendredi, samedi, dimanche) pour rester
 * sous le quota quotidien du fournisseur d'emails.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();            // jour de la vague (une par jour)
            $table->string('week');                    // semaine ISO concernée, ex. "2026-W30"
            $table->unsignedInteger('recipients')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_logs');
    }
};
