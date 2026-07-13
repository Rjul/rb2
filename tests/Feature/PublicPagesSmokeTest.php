<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Filet de sécurité AVANT la montée de version Laravel 9 -> 12.
 * Vérifie que les pages publiques clés répondent 200 avec un jeu de données minimal.
 * Les disques médias (dont le FTP vidéo) sont neutralisés via Storage::fake().
 */
class PublicPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Neutralise les disques distants (le FTP vidéo a throw=true et planterait).
        Storage::fake('emission_image');
        Storage::fake('emission_audio');
        Storage::fake('emission_video');
    }

    /**
     * Jeu de données minimal mais cohérent (programme actif + émissions publiées).
     */
    private function seedContent(): array
    {
        $user = User::factory()->create();

        $group = GroupProgramme::factory()->create(['is_active' => true]);

        $programme = Programme::factory()->create([
            'user_id'            => $user->id,
            'group_programme_id' => $group->id,
            'is_active'          => true,
            'is_archived'        => false,
            'has_rss'            => true,
        ]);

        $tag = Tag::factory()->create();

        $makeEmision = fn (string $type) => Emision::factory()->create([
            'user_id'      => $user->id,
            'programme_id' => $programme->id,
            'media_type'   => $type,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);

        $text  = $makeEmision(Emision::TYPE_TEXT);
        $audio = $makeEmision(Emision::TYPE_AUDIO);

        // L'audio a besoin d'un attachment, sinon detann plante sur ->first()->url.
        $attachment = Attachment::factory()->create([
            'name'          => 'demo-audio',
            'original_name' => 'demo.mp3',
            'mime'          => 'audio/mpeg',
            'extension'     => 'mp3',
            'size'          => 1234,
            'path'          => '2024/01/01/',
            'disk'          => 'emission_audio',
            'group'         => 'audio',
            'sort'          => 0,
            'user_id'       => $user->id,
        ]);
        $audio->attachment()->attach($attachment);

        return compact('user', 'group', 'programme', 'tag', 'text', 'audio');
    }

    public function test_homepage_repond_200(): void
    {
        $this->seedContent();
        $this->get('/')->assertOk();
    }

    public function test_liste_programme_repond_200(): void
    {
        ['programme' => $programme] = $this->seedContent();
        $this->get(route('list-programme', [$programme]))->assertOk();
    }

    public function test_liste_thematique_repond_200(): void
    {
        $this->seedContent();
        $this->get(route('list-tag', ['tag' => 'peu-importe']))->assertOk();
    }

    public function test_recherche_repond_200(): void
    {
        $this->seedContent();
        $this->get(route('list-search'))->assertOk();
        $this->get(route('list-search', ['query' => 'radio']))->assertOk();
    }

    public function test_informations_generales_repond_200(): void
    {
        $this->get('/informations-generales')->assertOk();
    }

    public function test_fiche_emission_texte_repond_200(): void
    {
        ['programme' => $programme, 'text' => $text] = $this->seedContent();
        $this->get(route('view-emision', [$programme, $text]))->assertOk();
    }

    public function test_fiche_emission_audio_repond_200(): void
    {
        ['programme' => $programme, 'audio' => $audio] = $this->seedContent();
        $this->get(route('view-emision', [$programme, $audio]))->assertOk();
    }

    public function test_fiche_emission_inactive_redirige_accueil(): void
    {
        ['programme' => $programme, 'text' => $text] = $this->seedContent();
        $text->update(['is_active' => false]);
        $this->get(route('view-emision', [$programme, $text]))
            ->assertRedirect(route('homepage'));
    }

    public function test_flux_rss_programme_repond_200(): void
    {
        ['programme' => $programme] = $this->seedContent();
        $response = $this->get(route('api-rss-programme', [$programme]));
        $response->assertOk();
        $this->assertStringContainsString('xml', strtolower($response->headers->get('Content-Type') ?? ''));
    }
}
