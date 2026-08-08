<?php

namespace Tests\Feature\Front;

use App\Livewire\V2\SearchPage;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use App\Support\FrontCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Comportements du catalogue v2 qui n'étaient couverts qu'en « 200 » : la
 * recherche filtre réellement, l'onglet type ne montre que son média, et les
 * catégories sont triées par poids (height) puis alphabétiquement. Ce sont des
 * régressions silencieuses (la page répond, mais le contenu serait faux).
 */
class V2CatalogueTest extends TestCase
{
    use RefreshDatabase;

    private GroupProgramme $category;
    private Programme $programme;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        User::factory()->create();
        $this->category  = GroupProgramme::factory()->create(['is_active' => 1]);
        $this->programme = Programme::factory()->create(['group_programme_id' => $this->category->id, 'is_active' => 1]);
    }

    private function publishedEmission(string $type, string $name, string $description = 'contenu neutre'): Emision
    {
        return Emision::factory()->create([
            'programme_id' => $this->programme->id,
            'media_type'   => $type,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
            'name'         => $name,
            'description'  => $description,
        ]);
    }

    public function test_la_recherche_ne_remonte_que_les_emissions_qui_matchent(): void
    {
        $this->publishedEmission(Emision::TYPE_AUDIO, 'ZJARDINmatch', 'chronique potager');
        $this->publishedEmission(Emision::TYPE_AUDIO, 'ZCUISINEother', 'recette du jour');

        Livewire::test(SearchPage::class)
            ->set('q', 'ZJARDINmatch')
            ->assertSee('ZJARDINmatch')
            ->assertDontSee('ZCUISINEother');
    }

    public function test_la_recherche_matche_aussi_la_description(): void
    {
        $this->publishedEmission(Emision::TYPE_AUDIO, 'ZTitreAnodin', 'reportage sur ZMOTdescription rare');

        Livewire::test(SearchPage::class)
            ->set('q', 'ZMOTdescription')
            ->assertSee('ZTitreAnodin');
    }

    public function test_la_recherche_exclut_les_emissions_non_publiees(): void
    {
        // Nom qui matche, mais émission inactive → ne doit pas apparaître.
        Emision::factory()->create([
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => false,
            'active_at'    => now()->subDay(),
            'name'         => 'ZINACTIFmatch',
            'description'  => 'x',
        ]);

        Livewire::test(SearchPage::class)
            ->set('q', 'ZINACTIFmatch')
            ->assertDontSee('ZINACTIFmatch');
    }

    public function test_l_onglet_audio_n_affiche_que_de_l_audio(): void
    {
        $this->publishedEmission(Emision::TYPE_AUDIO, 'ZAUDIOxyz');
        $this->publishedEmission(Emision::TYPE_VIDEO, 'ZVIDEOxyz');
        $this->publishedEmission(Emision::TYPE_TEXT, 'ZTEXTxyz');

        $this->get(route('v2.emissions.type', ['type' => 'audio']))
            ->assertOk()
            ->assertSee('ZAUDIOxyz')
            ->assertDontSee('ZVIDEOxyz')
            ->assertDontSee('ZTEXTxyz');
    }

    public function test_l_onglet_articles_n_affiche_que_les_articles(): void
    {
        $this->publishedEmission(Emision::TYPE_AUDIO, 'ZAUDIOxyz');
        $this->publishedEmission(Emision::TYPE_TEXT, 'ZARTICLExyz');

        // Le segment d'URL « articles » correspond au media_type « text ».
        $this->get(route('v2.emissions.type', ['type' => 'articles']))
            ->assertOk()
            ->assertSee('ZARTICLExyz')
            ->assertDontSee('ZAUDIOxyz');
    }

    public function test_les_categories_sont_triees_par_poids_puis_alphabetique(): void
    {
        // Poids identiques départagés par le nom ; poids plus lourd rejeté en fin.
        GroupProgramme::factory()->create(['is_active' => 1, 'height' => 3, 'name' => 'Zsort Aaa']); // (3, Aaa) → dernier
        GroupProgramme::factory()->create(['is_active' => 1, 'height' => 1, 'name' => 'Zsort Bbb']); // (1, Bbb) → premier
        GroupProgramme::factory()->create(['is_active' => 1, 'height' => 1, 'name' => 'Zsort Ccc']); // (1, Ccc) → deuxième

        FrontCache::bump(); // force le recalcul de l'index caché

        $html = $this->get(route('v2.categories'))->assertOk()->getContent();

        $posB = strpos($html, 'Zsort Bbb');
        $posC = strpos($html, 'Zsort Ccc');
        $posA = strpos($html, 'Zsort Aaa');

        $this->assertNotFalse($posB);
        $this->assertNotFalse($posC);
        $this->assertNotFalse($posA);

        // Ordre attendu : (1,Bbb) < (1,Ccc) < (3,Aaa).
        $this->assertTrue($posB < $posC, 'À poids égal, le tri alphabétique doit placer Bbb avant Ccc.');
        $this->assertTrue($posC < $posA, 'Le poids plus lourd (Aaa=3) doit passer après les poids légers.');
    }
}
