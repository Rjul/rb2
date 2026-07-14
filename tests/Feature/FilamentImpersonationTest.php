<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Orchid\Access\Impersonation;
use Tests\TestCase;

/**
 * « Se connecter en tant que » (impersonation) dans le back-office Filament,
 * reprise de la feature Orchid (Orchid\Access\Impersonation).
 */
class FilamentImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_usurper_un_utilisateur(): void
    {
        $admin = User::factory()->admin()->create();
        $cible = User::factory()->create(['name' => 'Contributeur']); // pas d'accès BO

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->callTableAction('impersonate', $cible)
            ->assertRedirect('/'); // sans platform.index → redirigé vers le site

        $this->assertTrue(Impersonation::isSwitch());
        $this->assertSame($cible->id, Auth::id());
    }

    public function test_usurpation_redirige_vers_la_gestion_si_acces_bo(): void
    {
        $admin = User::factory()->admin()->create();
        $cible = User::factory()->admin()->create(); // a platform.index

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->callTableAction('impersonate', $cible)
            ->assertRedirect('/gestion');
    }

    public function test_action_non_visible_sur_soi_meme(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('impersonate', $admin);
    }

    public function test_revenir_a_son_compte_restaure_l_original(): void
    {
        $admin = User::factory()->admin()->create();
        $cible = User::factory()->admin()->create();

        // On simule un état « en train d'usurper $cible, original = $admin ».
        $this->actingAs($cible)
            ->withSession([Impersonation::SESSION_NAME => $admin->id])
            ->get('/impersonation/leave')
            ->assertRedirect('/gestion')
            ->assertSessionMissing(Impersonation::SESSION_NAME);
    }
}
