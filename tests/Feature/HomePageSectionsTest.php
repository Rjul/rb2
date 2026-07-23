<?php

namespace Tests\Feature;

use App\Livewire\HomePage;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Sections de l'accueil : la ligne « Écouter/Lire/Voir » reste pleine (3 cartes)
 * même quand les émissions récentes de ce type sont déjà dans la grille du haut.
 */
class HomePageSectionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');
    }

    public function test_la_ligne_ecouter_reste_pleine_meme_si_audios_deja_dans_la_grille(): void
    {
        $user  = User::factory()->create();
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $prog  = Programme::factory()->create([
            'user_id' => $user->id, 'group_programme_id' => $group->id, 'is_active' => true,
        ]);

        // 6 audios récents (et rien d'autre) : héros + grille + à la une vont
        // consommer les plus récents, mais « Écouter » doit rester à 3.
        for ($i = 0; $i < 6; $i++) {
            Emision::factory()->create([
                'programme_id' => $prog->id,
                'user_id'      => $user->id,
                'media_type'   => 'audio',
                'is_active'    => true,
                'active_at'    => now()->subDays($i + 1),
            ]);
        }

        Livewire::test(HomePage::class)
            ->assertViewHas('audios', fn ($audios) => $audios->count() === 3);
    }
}
