<?php

namespace Tests\Feature;

use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Filet de sécurité pour le back-office Orchid après le bump Orchid 13 -> 14.
 * Vérifie que les écrans clés (dashboard, listes, formulaires d'édition) répondent 200
 * pour un administrateur — c'est la surface la plus à risque de la migration.
 */
class OrchidAdminSmokeTest extends TestCase
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
        return User::factory()->create([
            'permissions' => [
                'platform.index'              => true,
                'platform.systems.users'      => true,
                'platform.systems.roles'      => true,
                'platform.systems.attachment' => true,
                'platform.programmes'         => true,
                'platform.group.programme'    => true,
                'platform.annonces'           => true,
                'platform.themes'             => true,
                'platform.page-admin'         => true,
                'platform.commentaire'        => true,
            ],
        ]);
    }

    public function test_dashboard_repond_200(): void
    {
        $this->actingAs($this->admin())->get(route('platform.main'))->assertOk();
    }

    /**
     * @dataProvider ecransListe
     */
    public function test_les_listes_repondent_200(string $routeName): void
    {
        $this->actingAs($this->admin())->get(route($routeName))->assertOk();
    }

    public static function ecransListe(): array
    {
        return [
            'émissions'   => ['platform.emissions.list'],
            'programmes'  => ['platform.programme.list'],
            'groupes'     => ['platform.group.programme.list'],
            'thèmes'      => ['platform.tag.list'],
            'pages'       => ['platform.page-admin.list'],
            'annonces'    => ['platform.annonces.list'],
            'commentaires'=> ['platform.comments.list'],
            'users'       => ['platform.systems.users'],
        ];
    }

    /**
     * Les formulaires de création exercent les champs Orchid custom
     * (Cropper, Relation, Quill, UploadOverRide, Switcher) sur Orchid 14.
     *
     * @dataProvider ecransEdition
     */
    public function test_les_formulaires_creation_repondent_200(string $routeName): void
    {
        // Un groupe + programme pour peupler les champs Relation
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        Programme::factory()->create([
            'user_id'            => $this->admin()->id,
            'group_programme_id' => $group->id,
            'is_active'          => true,
        ]);

        $this->actingAs($this->admin())->get(route($routeName))->assertOk();
    }

    public static function ecransEdition(): array
    {
        return [
            'nouvel audio'   => ['platform.emission.edit'],
            'nouvelle vidéo' => ['platform.emission.video.edit'],
            'nouvel article' => ['platform.emission.text.edit'],
            'programme'      => ['platform.programme.edit'],
            'groupe'         => ['platform.group.programme.edit'],
            'thème'          => ['platform.tag.edit'],
            'page'           => ['platform.page-admin.edit'],
            'annonce'        => ['platform.annonce.edit'],
        ];
    }
}
