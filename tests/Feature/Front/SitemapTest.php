<?php

namespace Tests\Feature\Front;

use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\PageAdmin;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\User;
use App\Utilities\SitemapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Le sitemap liste les URL canoniques v2 des contenus PUBLIÉS, exclut le
 * non-publié et /recherche (noindex), et reste bien formé. On écrit dans un
 * fichier temporaire (pas de pollution du dépôt).
 */
class SitemapTest extends TestCase
{
    use RefreshDatabase;

    private GroupProgramme $category;
    private Programme $programme;
    private Emision $published;
    private Emision $hidden;
    private Tag $tag;
    private string $tmp;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        User::factory()->create();
        $this->category  = GroupProgramme::factory()->create(['is_active' => 1]);
        $this->programme = Programme::factory()->create(['group_programme_id' => $this->category->id, 'is_active' => 1]);

        $this->published = Emision::factory()->create([
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
            'name'         => 'ZPUBLIEE',
        ]);

        $this->hidden = Emision::factory()->create([
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => false,
            'active_at'    => now()->subDay(),
            'name'         => 'ZCACHEE',
        ]);

        $this->tag = Tag::factory()->create();
        $this->tag->emisions()->attach($this->published->id);

        $this->tmp = tempnam(sys_get_temp_dir(), 'sitemap') . '.xml';
    }

    protected function tearDown(): void
    {
        @unlink($this->tmp);
        parent::tearDown();
    }

    private function generate(): string
    {
        $this->assertTrue((new SitemapService())->generate($this->tmp));

        return file_get_contents($this->tmp);
    }

    public function test_le_sitemap_est_un_xml_bien_forme(): void
    {
        $xml = $this->generate();

        $this->assertNotFalse(simplexml_load_string($xml), 'Le sitemap doit être un XML bien formé.');
        $this->assertStringContainsString('<urlset', $xml);
    }

    public function test_le_sitemap_liste_les_contenus_publies(): void
    {
        $xml = $this->generate();

        $this->assertStringContainsString($this->published->canonicalUrl(), $xml);
        $this->assertStringContainsString($this->programme->canonicalUrl(), $xml);
        $this->assertStringContainsString($this->category->canonicalUrl(), $xml);
        $this->assertStringContainsString(
            route('v2.theme', ['tag' => $this->tag->getTranslation('slug', 'fr')]),
            $xml
        );
        // Hubs indexables.
        $this->assertStringContainsString(route('v2.home'), $xml);
        $this->assertStringContainsString(route('v2.categories'), $xml);
    }

    public function test_le_sitemap_liste_les_pages_editoriales(): void
    {
        PageAdmin::create(['path' => 'l-association', 'name' => 'L’association', 'content' => '<p>MCV</p>']);

        $xml = $this->generate();

        $this->assertStringContainsString(url('/l-association'), $xml);
        $this->assertStringContainsString(route('informations'), $xml);
    }

    public function test_le_sitemap_exclut_le_non_publie_et_la_recherche(): void
    {
        $xml = $this->generate();

        $this->assertStringNotContainsString($this->hidden->canonicalUrl(), $xml);
        $this->assertStringNotContainsString(route('v2.search'), $xml);
    }
}
