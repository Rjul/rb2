<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Codes à usage unique (OTP) pour la vérification d'adresse email :
 * inscription utilisateur (purpose = 'register') et, si besoin, autres flux.
 * Le code est stocké HACHÉ (jamais en clair) ; il expire et le nombre de
 * tentatives est plafonné (anti brute-force).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('purpose', 32)->default('register');
            $table->string('code');            // haché (Hash::make)
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['email', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_otps');
    }
};
