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
 * Smoke test des surfaces publiques restées « hors v2 » : la page éditoriale
 * legacy et le flux RSS. Le front v2 (accueil, catégories, programmes, émissions,
 * thèmes) est couvert par {@see Front\V2PagesTest} et les redirections des
 * anciennes URL par {@see Front\CutoverRedirectsTest}.
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
        Tag::factory()->create();

        $audio = Emision::factory()->create([
            'user_id'      => $user->id,
            'programme_id' => $programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);
        $attachment = Attachment::factory()->create([
            'name' => 'demo-audio', 'original_name' => 'demo.mp3', 'mime' => 'audio/mpeg',
            'extension' => 'mp3', 'size' => 1234, 'path' => '2024/01/01/',
            'disk' => 'emission_audio', 'group' => 'audio', 'sort' => 0, 'user_id' => $user->id,
        ]);
        $audio->attachment()->attach($attachment);

        return compact('user', 'group', 'programme', 'audio');
    }

    public function test_informations_generales_repond_200(): void
    {
        $this->get('/informations-generales')->assertOk();
    }

    public function test_flux_rss_programme_repond_200(): void
    {
        ['programme' => $programme] = $this->seedContent();

        $response = $this->get(route('api-rss-programme', [$programme]));

        $response->assertOk();
        $this->assertStringContainsString('xml', strtolower($response->headers->get('Content-Type') ?? ''));

        // Le flux doit émettre les URL canoniques v2 (post-bascule) : <link> programme et
        // <guid> émission pointent sur /categories/…, et plus sur l'ancienne page émission
        // /programme-…/emission-… (repérable par le fragment legacy « /emission- »).
        // NB : le path du flux lui-même est « api/rss/programme-{slug} » → « /programme- »
        // apparaît légitimement dans <itunes:image> (URL::full), ce n'est pas un lien de contenu.
        $body = $response->getContent();
        $this->assertStringContainsString('/categories/', $body, 'Le RSS doit pointer sur les URL v2 (/categories/…).');
        $this->assertStringNotContainsString('/emission-', $body, 'Le RSS ne doit plus émettre l’URL legacy de la page émission (/…/emission-…).');
    }
}
