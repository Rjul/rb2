<?php

namespace Tests\Feature;

use App\Filament\Resources\CommentResource;
use App\Filament\Resources\EmisionResource;
use App\Filament\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Resources\RoleResource\Pages\EditRole;
use App\Filament\Resources\TagResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Support\PermissionField;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Vérifie que le back-office Filament applique les permissions Orchid :
 * - gating par ressource (un slug de permission unique)
 * - scoping des lignes émissions/programmes selon les programmes autorisés
 * Le front n'est PAS concerné : ces droits ne s'appliquent qu'au panel /gestion.
 */
class FilamentPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
    }

    public function test_ressource_refusee_sans_la_permission(): void
    {
        // Accès au panel (platform.index) mais SANS la permission « Thèmes ».
        $user = User::factory()->create(['permissions' => ['platform.index' => true]]);

        $this->actingAs($user);
        $this->assertFalse(TagResource::canAccess(), 'Sans le slug, la ressource ne doit pas être accessible');
        $this->assertFalse(TagResource::shouldRegisterNavigation(), 'Sans le slug, le menu ne doit pas apparaître');
        $this->get('/gestion/tags')->assertForbidden();
    }

    public function test_ressource_autorisee_avec_la_permission(): void
    {
        $user = User::factory()->create([
            'permissions' => ['platform.index' => true, 'platform.themes' => true],
        ]);

        $this->actingAs($user);
        $this->assertTrue(TagResource::canAccess());
        $this->get('/gestion/tags')->assertOk();
    }

    public function test_permissions_cumulees_via_role(): void
    {
        // La permission arrive par un rôle, pas en direct → doit tout de même donner l'accès.
        $role = \App\Models\Role::create([
            'name' => 'Modérateur', 'slug' => 'moderateur',
            'permissions' => ['platform.commentaire' => true],
        ]);
        $user = User::factory()->create(['permissions' => ['platform.index' => true]]);
        $user->roles()->attach($role);

        $this->actingAs($user->fresh());
        $this->assertTrue(CommentResource::canAccess(), 'La permission héritée d’un rôle doit se cumuler');
    }

    public function test_super_admin_a_tout(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);
        $this->assertTrue(TagResource::canAccess());
        $this->assertTrue(UserResource::canAccess());
        $this->assertTrue(CommentResource::canAccess());
    }

    public function test_liste_emissions_limitee_aux_programmes_autorises(): void
    {
        User::factory()->create(); // satisfait la dépendance user_id aléatoire des factories
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $prog1 = Programme::factory()->create(['group_programme_id' => $group->id]);
        $prog2 = Programme::factory()->create(['group_programme_id' => $group->id]);

        $autorisee = Emision::factory()->create(['programme_id' => $prog1->id, 'media_type' => 'text']);
        Emision::factory()->create(['programme_id' => $prog2->id, 'media_type' => 'text']);

        // Autorisé uniquement sur le programme 1.
        $user = User::factory()->create([
            'permissions' => ['platform.index' => true, 'platform.emission.' . $prog1->id => true],
        ]);
        $this->actingAs($user);

        $ids = EmisionResource::getEloquentQuery()->pluck('id')->all();
        $this->assertSame([$autorisee->id], $ids, 'Ne doit voir que les émissions du programme autorisé');
    }

    public function test_permissions_groupees_round_trip(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        // Prend la 1re permission réellement enregistrée (en test, seules les
        // permissions natives d'Orchid le sont : les tables n'existent pas au boot).
        $slug = null;
        $groupKey = null;
        foreach (PermissionField::groups() as $gk => $group) {
            if (! empty($group['options'])) {
                $groupKey = $gk;
                $slug = array_key_first($group['options']);
                break;
            }
        }
        $this->assertNotNull($slug, 'Au moins une catégorie de permissions doit exister');
        $field = PermissionField::transientKey($groupKey);

        // Création : cocher la permission via sa catégorie → map {slug: true}.
        Livewire::test(CreateRole::class)
            ->fillForm(['name' => 'Rôle test', 'slug' => 'role-test', $field => [$slug]])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::where('slug', 'role-test')->first();
        $this->assertNotNull($role);
        $this->assertTrue((bool) ($role->permissions[$slug] ?? false), 'La permission cochée doit être stockée en map {slug: true}');

        // Édition : la case doit être re-cochée (hydratation depuis la map).
        Livewire::test(EditRole::class, ['record' => $role->getKey()])
            ->assertFormSet([$field => [$slug]]);
    }

    public function test_permissions_utilisateur_hydratees_a_l_edition(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $slug = null;
        $groupKey = null;
        foreach (PermissionField::groups() as $gk => $group) {
            if (! empty($group['options'])) {
                $groupKey = $gk;
                $slug = array_key_first($group['options']);
                break;
            }
        }
        $this->assertNotNull($slug);

        // permissions est dans $hidden sur User : ce test garde l'hydratation via le record.
        $cible = User::factory()->create(['permissions' => [$slug => true]]);

        Livewire::test(EditUser::class, ['record' => $cible->getKey()])
            ->assertFormSet([PermissionField::transientKey($groupKey) => [$slug]]);
    }

    public function test_super_admin_voit_toutes_les_emissions(): void
    {
        $admin = User::factory()->admin()->create(); // existe avant → sert aussi de user_id aléatoire
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $prog1 = Programme::factory()->create(['group_programme_id' => $group->id]);
        $prog2 = Programme::factory()->create(['group_programme_id' => $group->id]);
        Emision::factory()->create(['programme_id' => $prog1->id, 'media_type' => 'text']);
        Emision::factory()->create(['programme_id' => $prog2->id, 'media_type' => 'text']);

        $this->actingAs($admin);

        $this->assertCount(2, EmisionResource::getEloquentQuery()->pluck('id')->all());
    }
}
