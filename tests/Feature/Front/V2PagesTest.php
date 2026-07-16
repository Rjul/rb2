<?php

namespace Tests\Feature\Front;

use App\Livewire\V2\CommentThread;
use App\Models\Comment;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class V2PagesTest extends TestCase
{
    use RefreshDatabase;

    private GroupProgramme $category;
    private Programme $programme;
    private Emision $emission;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        // Graphe minimal actif : User → Catégorie → Programme → Émission audio publiée.
        User::factory()->create();
        $this->category  = GroupProgramme::factory()->create(['is_active' => 1]);
        $this->programme = Programme::factory()->create(['group_programme_id' => $this->category->id, 'is_active' => 1]);
        $this->emission  = Emision::factory()->create([
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);
    }

    public function test_les_hubs_v2_repondent_200(): void
    {
        foreach (['v2.home', 'v2.categories', 'v2.programmes', 'v2.emissions', 'v2.themes', 'v2.search'] as $name) {
            $this->get(route($name))->assertOk();
        }
        $this->get(route('v2.emissions.type', ['type' => 'audio']))->assertOk();
    }

    public function test_les_feuilles_v2_repondent_200(): void
    {
        $this->get($this->category->canonicalUrl())->assertOk()->assertSee($this->category->name);
        $this->get($this->programme->canonicalUrl())->assertOk()->assertSee($this->programme->name);
        $this->get($this->emission->canonicalUrl())->assertOk()->assertSee($this->emission->name);
    }

    public function test_canonical_self_heal_redirige_en_301(): void
    {
        // Ancêtres erronés → 301 vers l'URL canonique (résolution par le dernier segment).
        $wrong = '/v2/categories/mauvaise-cat/mauvais-prog/' . $this->emission->slug;

        $this->get($wrong)
            ->assertStatus(301)
            ->assertRedirect($this->emission->canonicalUrl());
    }

    public function test_emission_inactive_renvoie_404(): void
    {
        $this->emission->update(['is_active' => false]);

        $this->get($this->emission->canonicalUrl())->assertNotFound();
    }

    public function test_le_fil_de_commentaires_invite_a_se_connecter(): void
    {
        Livewire::test(CommentThread::class, ['emission' => $this->emission])
            ->assertSee('Connectez-vous');
    }

    public function test_un_utilisateur_connecte_poste_un_commentaire_en_moderation(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CommentThread::class, ['emission' => $this->emission])
            ->set('body', 'Très bonne émission, merci !')
            ->call('addComment')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('comments', [
            'comment'          => 'Très bonne émission, merci !',
            'approved'         => false,
            'commentable_id'   => $this->emission->id,
            'commentable_type' => Emision::class,
        ]);
    }

    public function test_l_edition_d_un_commentaire_repasse_en_moderation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $comment = new Comment(['comment' => 'Commentaire approuvé', 'approved' => true]);
        $comment->commenter()->associate($user);
        $comment->commentable()->associate($this->emission);
        $comment->save();

        Livewire::test(CommentThread::class, ['emission' => $this->emission])
            ->call('startEdit', $comment->id)
            ->set('editBody', 'contenu modifié après coup')
            ->call('saveEdit')
            ->assertHasNoErrors();

        // Le commentaire édité redevient non approuvé (relecture obligatoire).
        $this->assertDatabaseHas('comments', [
            'id'       => $comment->id,
            'comment'  => 'contenu modifié après coup',
            'approved' => false,
        ]);
    }

    public function test_le_jsonld_echappe_les_balises_script(): void
    {
        $cat = GroupProgramme::factory()->create([
            'is_active' => 1,
            'name'      => '</script><script>alert(1)</script>',
        ]);

        $this->get($cat->canonicalUrl())
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_le_cache_front_est_invalide_par_une_ecriture_bo(): void
    {
        $calls = 0;
        $fn = function () use (&$calls) { return ++$calls; };

        // 1er appel : calcule ; 2e : servi par le cache.
        $this->assertSame(1, \App\Support\FrontCache::remember('test:key', $fn));
        $this->assertSame(1, \App\Support\FrontCache::remember('test:key', $fn));

        // Écriture BO (saved) → la version tourne → recalcul.
        $this->emission->update(['name' => 'Titre modifié']);
        $this->assertSame(2, \App\Support\FrontCache::remember('test:key', $fn));
    }

    public function test_le_ttl_du_cache_expire_au_prochain_top_d_heure(): void
    {
        $ttl = \App\Support\FrontCache::defaultTtl();

        // Entre 1 s et 3600 s, et cale exactement sur le prochain top d'heure.
        $this->assertGreaterThanOrEqual(1, $ttl);
        $this->assertLessThanOrEqual(3600, $ttl);
        $expected = now()->copy()->startOfHour()->addHour()->getTimestamp() - now()->getTimestamp();
        $this->assertSame(max(1, $expected), $ttl);
    }

    public function test_les_emissions_non_publiees_ne_sont_pas_visibles(): void
    {
        // 3 formes de non-publication, chacune repérable par un nom unique.
        $futur = Emision::factory()->create([
            'programme_id' => $this->programme->id, 'media_type' => Emision::TYPE_AUDIO,
            'is_active' => true, 'active_at' => now()->addDay(), 'name' => 'ZFUTURxyz',
        ]);
        $inactif = Emision::factory()->create([
            'programme_id' => $this->programme->id, 'media_type' => Emision::TYPE_AUDIO,
            'is_active' => false, 'active_at' => now()->subDay(), 'name' => 'ZINACTIFxyz',
        ]);
        $progOff = Programme::factory()->create(['group_programme_id' => $this->category->id, 'is_active' => false]);
        $sousProgOff = Emision::factory()->create([
            'programme_id' => $progOff->id, 'media_type' => Emision::TYPE_AUDIO,
            'is_active' => true, 'active_at' => now()->subDay(), 'name' => 'ZPROGOFFxyz',
        ]);

        // Les fiches renvoient 404.
        foreach ([$futur, $inactif, $sousProgOff] as $hidden) {
            $this->get($hidden->canonicalUrl())->assertNotFound();
        }

        // Aucune surface publique ne les liste (accueil, catalogue, hubs).
        foreach (['v2.home', 'v2.emissions', 'v2.themes', 'v2.categories', 'v2.programmes'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertDontSee('ZFUTURxyz')
                ->assertDontSee('ZINACTIFxyz')
                ->assertDontSee('ZPROGOFFxyz');
        }

        // La page du programme actif n'affiche ni le futur ni l'inactif.
        $this->get($this->programme->canonicalUrl())->assertOk()
            ->assertDontSee('ZFUTURxyz')
            ->assertDontSee('ZINACTIFxyz');
    }

    public function test_la_fiche_n_expose_pas_une_emission_programmee_en_voisin(): void
    {
        // Une émission future du même programme ne doit pas apparaître (ni "suivant", ni suggestion).
        Emision::factory()->create([
            'programme_id' => $this->programme->id, 'media_type' => Emision::TYPE_AUDIO,
            'is_active' => true, 'active_at' => now()->addWeek(), 'name' => 'ZVOISINFUTURxyz',
        ]);

        $this->get($this->emission->canonicalUrl())
            ->assertOk()
            ->assertDontSee('ZVOISINFUTURxyz');
    }

    public function test_la_pagination_expose_des_liens_crawlables(): void
    {
        Emision::factory()->count(20)->create([
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);

        $this->get(route('v2.emissions'))
            ->assertOk()
            ->assertSee('page=2', false)
            ->assertSee('wire:navigate', false);
    }
}
