<?php

namespace Tests\Feature;

use App\Filament\Resources\EmisionResource\Pages\ListEmisions;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Liste des émissions dans Filament : badge/filtre « Statut » (reflète la
 * visibilité publique réelle) et action « Dupliquer » (copie en brouillon).
 */
class FilamentEmisionListTest extends TestCase
{
    use RefreshDatabase;

    private Programme $programme;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $category        = GroupProgramme::factory()->create(['is_active' => 1]);
        $this->programme = Programme::factory()->create(['group_programme_id' => $category->id, 'is_active' => 1]);
    }

    private function emission(array $overrides = []): Emision
    {
        return Emision::factory()->create($overrides + [
            'programme_id' => $this->programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);
    }

    public function test_le_filtre_statut_isole_les_programmees(): void
    {
        $publiee    = $this->emission(['name' => 'Publiée A']);
        $programmee = $this->emission(['name' => 'Future B', 'active_at' => now()->addWeek()]);
        $brouillon  = $this->emission(['name' => 'Brouillon C', 'is_active' => false]);

        Livewire::test(ListEmisions::class)
            ->filterTable('statut', 'programmee')
            ->assertCanSeeTableRecords([$programmee])
            ->assertCanNotSeeTableRecords([$publiee, $brouillon]);
    }

    public function test_le_filtre_statut_isole_les_brouillons(): void
    {
        $publiee   = $this->emission(['name' => 'Publiée A']);
        $brouillon = $this->emission(['name' => 'Brouillon C', 'is_active' => false]);

        Livewire::test(ListEmisions::class)
            ->filterTable('statut', 'brouillon')
            ->assertCanSeeTableRecords([$brouillon])
            ->assertCanNotSeeTableRecords([$publiee]);
    }

    public function test_le_badge_reflete_le_statut(): void
    {
        $programmee = $this->emission(['active_at' => now()->addWeek()]);
        $brouillon  = $this->emission(['is_active' => false]);
        $publiee    = $this->emission();

        Livewire::test(ListEmisions::class)
            ->assertTableColumnStateSet('statut', 'Programmée', $programmee)
            ->assertTableColumnStateSet('statut', 'Brouillon', $brouillon)
            ->assertTableColumnStateSet('statut', 'Publiée', $publiee);
    }

    public function test_dupliquer_cree_un_brouillon_complet(): void
    {
        $tag      = Tag::factory()->create();
        $original = $this->emission(['name' => 'Ma chronique hebdo', 'is_put_forward' => true]);
        $original->tags()->attach($tag->id);

        Livewire::test(ListEmisions::class)
            ->callTableAction('dupliquer', $original);

        $copy = Emision::where('name', 'Ma chronique hebdo (copie)')->first();

        $this->assertNotNull($copy, 'La copie doit être créée');
        $this->assertFalse((bool) $copy->is_active, 'La copie est un brouillon');
        $this->assertFalse((bool) $copy->is_put_forward, 'La copie n’est pas à la une');
        $this->assertNotSame($original->slug, $copy->slug, 'Le slug est régénéré');
        $this->assertStringEndsWith('-' . $copy->id, $copy->slug, 'Le slug de la copie contient son id');
        $this->assertSame($original->programme_id, $copy->programme_id);
        $this->assertSame($original->description, $copy->description);
        $this->assertTrue($copy->tags()->where('tags.id', $tag->id)->exists(), 'Les thèmes sont repris');
        $this->assertSame(0, $copy->attachment()->count(), 'Les fichiers ne sont pas copiés');
    }
}
