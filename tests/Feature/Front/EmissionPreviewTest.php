<?php

namespace Tests\Feature\Front;

use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Préversion des émissions NON publiées : les utilisateurs back-office
 * (permission platform.index — même porte que /gestion) voient la fiche avec un
 * bandeau « Prévisualisation » + noindex ; tout le monde d'autre reste sur 404.
 */
class EmissionPreviewTest extends TestCase
{
    use RefreshDatabase;

    private Programme $programme;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        User::factory()->create(); // les factories Programme/Emision piochent un user existant
        $category        = GroupProgramme::factory()->create(['is_active' => 1]);
        $this->programme = Programme::factory()->create(['group_programme_id' => $category->id, 'is_active' => 1]);
    }

    private function draft(array $overrides = []): Emision
    {
        return Emision::factory()->create($overrides + [
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => false,
            'active_at'    => now()->subDay(),
        ]);
    }

    private function backOfficeUser(): User
    {
        return User::factory()->create(['permissions' => ['platform.index' => true]]);
    }

    public function test_un_visiteur_reste_sur_404(): void
    {
        $this->get($this->draft()->canonicalUrl())->assertNotFound();
    }

    public function test_un_membre_sans_acces_bo_reste_sur_404(): void
    {
        $this->actingAs(User::factory()->create(['permissions' => []]));

        $this->get($this->draft()->canonicalUrl())->assertNotFound();
    }

    public function test_un_utilisateur_bo_previsualise_un_brouillon(): void
    {
        $this->actingAs($this->backOfficeUser());

        $this->get($this->draft()->canonicalUrl())
            ->assertOk()
            ->assertSee('Prévisualisation')
            ->assertSee('Brouillon')
            ->assertSee('/gestion/emisions/', false)
            // Jamais indexée.
            ->assertSee('name="robots" content="noindex, nofollow"', false);
    }

    public function test_un_utilisateur_bo_previsualise_une_programmee(): void
    {
        $this->actingAs($this->backOfficeUser());

        $scheduled = $this->draft(['is_active' => true, 'active_at' => now()->addWeek()]);

        $this->get($scheduled->canonicalUrl())
            ->assertOk()
            ->assertSee('Prévisualisation')
            ->assertSee('Programmée le');
    }

    public function test_une_emission_publiee_reste_sans_bandeau_pour_le_bo(): void
    {
        $this->actingAs($this->backOfficeUser());

        $published = $this->draft(['is_active' => true, 'active_at' => now()->subDay()]);

        $this->get($published->canonicalUrl())
            ->assertOk()
            ->assertDontSee('Prévisualisation')
            ->assertDontSee('name="robots" content="noindex, nofollow"', false);
    }
}
