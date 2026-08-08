<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index de performance pour le front v2.
 *
 * Avant : `emisions` n'avait que PRIMARY + clés étrangères. Or chaque page du
 * front filtre `WHERE is_active AND active_at < now ORDER BY active_at DESC`
 * (scan + filesort) et chaque redirection d'ancienne URL fait `WHERE slug = ?`
 * (scan). Au recrawl post-bascule, Google frappe des milliers de 301 d'un coup.
 *
 * Les modèles n'utilisent PAS SoftDeletes (la colonne `deleted_at` est un
 * vestige Orchid non scopé) → inutile de l'inclure dans les index.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emisions', function (Blueprint $table) {
            // Filtre + tri universels (accueil, recherche, programme, catégorie, thème, catalogue).
            $table->index(['is_active', 'active_at'], 'emisions_active_idx');
            // fromSlugId() : toutes les redirections 301 + le self-healing canonique.
            $table->index('slug', 'emisions_slug_idx');
            // Onglets /emissions/{type}.
            $table->index(['media_type', 'is_active', 'active_at'], 'emisions_media_active_idx');
        });

        Schema::table('programmes', function (Blueprint $table) {
            $table->index('slug', 'programmes_slug_idx');
            $table->index(['is_active', 'height'], 'programmes_active_height_idx');
        });

        Schema::table('group_programmes', function (Blueprint $table) {
            $table->index(['is_active', 'height'], 'group_programmes_active_height_idx');
        });
    }

    public function down(): void
    {
        Schema::table('emisions', function (Blueprint $table) {
            $table->dropIndex('emisions_active_idx');
            $table->dropIndex('emisions_slug_idx');
            $table->dropIndex('emisions_media_active_idx');
        });

        Schema::table('programmes', function (Blueprint $table) {
            $table->dropIndex('programmes_slug_idx');
            $table->dropIndex('programmes_active_height_idx');
        });

        Schema::table('group_programmes', function (Blueprint $table) {
            $table->dropIndex('group_programmes_active_height_idx');
        });
    }
};
