<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le seeder de démonstration produit un jeu de données cohérent, non-interactif,
 * et complet (toutes les surfaces du front ont de quoi s'afficher).
 */
class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_seeder_produit_des_donnees_coherentes(): void
    {
        // Doit s'exécuter sans confirmation interactive (env testing ≠ production).
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, GroupProgramme::count());
        $this->assertGreaterThan(0, Programme::count());
        $this->assertGreaterThan(0, Emision::count());

        // Les 3 types de média sont présents (audio / vidéo / article).
        $types = Emision::query()->distinct()->pluck('media_type')->all();
        foreach (['audio', 'video', 'text'] as $type) {
            $this->assertContains($type, $types, "Le type d'émission « {$type} » doit être présent");
        }

        // Au moins une émission « à la une » publiée cette semaine, avec catégorie + programme
        // → alimente l'accueil (« à la une de la semaine ») et la newsletter.
        $this->assertTrue(
            Emision::query()
                ->where('is_put_forward', true)
                ->where('is_active', true)
                ->whereBetween('active_at', [now()->subWeek(), now()])
                ->whereHas('programme', fn ($q) => $q->where('is_active', true)->whereNotNull('group_programme_id'))
                ->exists(),
            'Il faut au moins une émission à la une publiée cette semaine.'
        );

        // Des commentaires approuvés existent (visibles sur le front).
        $this->assertGreaterThan(0, Comment::where('approved', true)->count());
    }
}
