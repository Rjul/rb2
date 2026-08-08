<?php

namespace Tests\Feature\Front;

use App\Models\PageAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pages éditoriales (BO + « Informations générales ») migrées sur le layout v2 :
 * rendues dans la stack TALL (header/footer/lecteur persistant), contenu BO
 * assaini, URL inchangées (catch-all /{pageAdmin:path} en dernier).
 */
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    /** Marqueur propre au layout v2 (lien d'évitement du layout tall). */
    private const V2_MARKER = 'Aller au contenu';

    private function makePage(array $overrides = []): PageAdmin
    {
        return PageAdmin::create($overrides + [
            'path'    => 'l-association',
            'name'    => 'L’association',
            'content' => '<h1>L’association</h1><p>Nom : Médias Citoyens en Villeneuvois.</p>',
        ]);
    }

    public function test_une_page_administrable_est_rendue_dans_le_layout_v2(): void
    {
        $page = $this->makePage();

        $this->get('/' . $page->path)
            ->assertOk()
            ->assertSee($page->name)
            ->assertSee('Médias Citoyens en Villeneuvois')
            ->assertSee(self::V2_MARKER)
            ->assertSee('<link rel="canonical" href="' . url('/' . $page->path) . '">', false);
    }

    public function test_un_path_accentue_est_servi(): void
    {
        // La prod a « responsabilité-legale » (accent dans l'URL).
        $page = $this->makePage(['path' => 'responsabilité-legale', 'name' => 'La responsabilité légale']);

        $this->get('/responsabilité-legale')
            ->assertOk()
            ->assertSee($page->name)
            ->assertSee(self::V2_MARKER);
    }

    public function test_le_contenu_bo_est_assaini(): void
    {
        $this->makePage([
            'path'    => 'page-piegee',
            'name'    => 'Page piégée',
            'content' => '<p>Texte sain</p><script>alert(1)</script>',
        ]);

        $this->get('/page-piegee')
            ->assertOk()
            ->assertSee('Texte sain')
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_un_path_inconnu_renvoie_404(): void
    {
        $this->get('/page-qui-n-existe-pas')->assertNotFound();
    }

    public function test_informations_generales_est_rendue_dans_le_layout_v2(): void
    {
        $this->get('/informations-generales')
            ->assertOk()
            ->assertSee('Informations générales')
            ->assertSee('Médias Citoyens en Villeneuvois')
            ->assertSee(self::V2_MARKER)
            ->assertSee('<link rel="canonical" href="' . url('/informations-generales') . '">', false);
    }
}
