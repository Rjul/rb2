<?php

namespace Tests\Feature\Front;

use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Bascule v1 → v2 : les anciennes URL redirigent en 301 vers la page v2 canonique,
 * et les pages v2 sont servies à la racine (indexables).
 */
class CutoverRedirectsTest extends TestCase
{
    use RefreshDatabase;

    private GroupProgramme $category;
    private Programme $programme;
    private Emision $emission;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        User::factory()->create();
        $this->category  = GroupProgramme::factory()->create(['is_active' => 1]);
        $this->programme = Programme::factory()->create(['group_programme_id' => $this->category->id, 'is_active' => 1]);
        $this->emission  = Emision::factory()->create([
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);
        $this->tag = Tag::factory()->create();
    }

    public function test_ancienne_url_programme_redirige_301(): void
    {
        $this->get('/programme-' . $this->programme->slug)
            ->assertStatus(301)
            ->assertRedirect($this->programme->canonicalUrl());
    }

    public function test_ancienne_url_emission_redirige_301(): void
    {
        $this->get('/programme-' . $this->programme->slug . '/emission-' . $this->emission->slug)
            ->assertStatus(301)
            ->assertRedirect($this->emission->canonicalUrl());
    }

    public function test_ancienne_url_emission_resiliente_id_in_slug(): void
    {
        // Ancêtres/slug erronés mais id en fin de segment → résolution + 301 canonique.
        $this->get('/programme-nimporte/emission-titre-perime-' . $this->emission->id)
            ->assertStatus(301)
            ->assertRedirect($this->emission->canonicalUrl());
    }

    public function test_ancienne_url_programme_resiliente_id_in_slug(): void
    {
        // Slug programme périmé (migration) mais id en fin de segment → 301 canonique.
        $this->get('/programme-ancien-slug-perime-' . $this->programme->id)
            ->assertStatus(301)
            ->assertRedirect($this->programme->canonicalUrl());
    }

    public function test_ancien_lien_court_emisiones_redirige_301(): void
    {
        $this->get('/emisiones/' . $this->emission->id)
            ->assertStatus(301)
            ->assertRedirect($this->emission->canonicalUrl());
    }

    public function test_ancien_programas_redirige_301(): void
    {
        $this->get('/programas?id=' . $this->programme->id)
            ->assertStatus(301)
            ->assertRedirect($this->programme->canonicalUrl());

        // Sans id valide → 301 vers la recherche v2.
        $this->get('/programas')
            ->assertStatus(301)
            ->assertRedirect(route('v2.search'));
    }

    public function test_les_chemins_repris_du_v1_servent_le_v2_en_200(): void
    {
        $this->get('/')->assertOk();                                   // accueil v2
        $this->get('/recherche')->assertOk();                          // recherche v2
        $this->get('/thematique-' . $this->tag->slug)->assertOk();     // thème v2
    }

    public function test_les_hubs_v2_a_la_racine_repondent_200(): void
    {
        foreach (['/categories', '/programmes', '/emissions', '/emissions/audio', '/thematiques'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_indexation_contenu_indexable_recherche_noindex(): void
    {
        // Page de contenu : plus de noindex.
        $this->get($this->emission->canonicalUrl())
            ->assertOk()
            ->assertDontSee('name="robots" content="noindex', false);

        // Recherche : toujours noindex (voulu).
        $this->get('/recherche')
            ->assertOk()
            ->assertSee('name="robots" content="noindex', false);
    }
}
