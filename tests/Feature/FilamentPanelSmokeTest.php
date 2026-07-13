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
        return User::factory()->create([
            'permissions' => ['platform.index' => true],
        ]);
    }

    public function test_dashboard_repond_200(): void
    {
        $this->actingAs($this->admin())->get('/gestion')->assertOk();
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
        ];
    }
}
