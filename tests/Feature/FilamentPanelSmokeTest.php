<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Filet de sécurité pour le nouveau panel Filament (/gestion), en coexistence avec Orchid.
 * Vérifie que le dashboard et l'index de chaque Resource répondent 200 pour un admin.
 */
class FilamentPanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');
        Storage::fake('emission_video');
    }

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_dashboard_repond_200(): void
    {
        $this->actingAs($this->admin())->get('/gestion')->assertOk();
    }

    /** Le menu du panel liste bien la Newsletter pour un compte qui a la permission. */
    public function test_le_menu_contient_la_newsletter(): void
    {
        $this->actingAs($this->admin())
            ->get('/gestion')
            ->assertOk()
            ->assertSee('Newsletter');
    }

    /**
     * @dataProvider resources
     */
    public function test_index_resource_repond_200(string $slug): void
    {
        $this->actingAs($this->admin())->get("/gestion/{$slug}")->assertOk();
    }

    public static function resources(): array
    {
        return [
            'groupes'      => ['group-programmes'],
            'programmes'   => ['programmes'],
            'émissions'    => ['emisions'],
            'thèmes'       => ['tags'],
            'pages'        => ['page-admins'],
            'annonces'     => ['website-news'],
            'commentaires' => ['comments'],
            'rôles'        => ['roles'],
            'utilisateurs' => ['users'],
        ];
    }

    /**
     * @dataProvider createResources
     */
    public function test_page_creation_repond_200(string $slug): void
    {
        $admin = $this->admin();
        $group = \App\Models\GroupProgramme::factory()->create(['is_active' => true]);
        \App\Models\Programme::factory()->create([
            'user_id' => $admin->id,
            'group_programme_id' => $group->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get("/gestion/{$slug}/create")->assertOk();
    }

    public function test_pages_edition_avec_donnees(): void
    {
        $admin = $this->admin();
        $group = \App\Models\GroupProgramme::factory()->create(['is_active' => true]);
        $programme = \App\Models\Programme::factory()->create([
            'user_id' => $admin->id, 'group_programme_id' => $group->id, 'is_active' => true,
        ]);
        $tag = \App\Models\Tag::factory()->create();
        $emision = \App\Models\Emision::factory()->create([
            'user_id' => $admin->id, 'programme_id' => $programme->id,
            'media_type' => \App\Models\Emision::TYPE_AUDIO, 'is_active' => true,
        ]);
        $emision->attachTags([$tag]);
        $role = \App\Models\Role::create([
            'name' => 'Éditeur', 'slug' => 'editeur',
            'permissions' => ['platform.index' => true, 'platform.programmes' => true],
        ]);

        $this->actingAs($admin);
        $this->get("/gestion/group-programmes/{$group->id}/edit")->assertOk();
        $this->get("/gestion/programmes/{$programme->id}/edit")->assertOk();
        $this->get("/gestion/tags/{$tag->id}/edit")->assertOk();
        $this->get("/gestion/emisions/{$emision->id}/edit")->assertOk();
        $this->get("/gestion/roles/{$role->id}/edit")->assertOk();
        $this->get("/gestion/users/{$admin->id}/edit")->assertOk();
    }

    public static function createResources(): array
    {
        return [
            'groupes'    => ['group-programmes'],
            'programmes' => ['programmes'],
            'émissions'  => ['emisions'],
            'thèmes'     => ['tags'],
            'pages'      => ['page-admins'],
            'annonces'   => ['website-news'],
            'rôles'      => ['roles'],
            'utilisateurs' => ['users'],
        ];
    }
}
