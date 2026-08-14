<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Attachments migrés de l'ancienne base (2020) : extension stockée DANS `name`
 * (« 90.mp3 »), colonne `extension` vide → toutes les URL sortaient avec un
 * point final (« old/0/90.mp3. ») → 404. Deux remèdes testés ici :
 * l'accesseur défensif (pas de point orphelin) et la migration de réparation.
 */
class LegacyAttachmentExtensionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_audio');
        Storage::fake('emission_image');
        User::factory()->create();
    }

    private function legacyAttachment(array $overrides = []): Attachment
    {
        return Attachment::factory()->create($overrides + [
            'name'          => '90.mp3',   // extension incluse (défaut migrate:old_db)
            'extension'     => null,       // colonne vide
            'original_name' => '90.mp3',
            'mime'          => 'audio/*',
            'path'          => 'old/0/',
            'disk'          => 'emission_audio',
            'group'         => 'audio',
            'size'          => 123,
            'sort'          => 0,
        ]);
    }

    public function test_audio_url_sans_point_final_sur_une_ligne_2020(): void
    {
        $category  = GroupProgramme::factory()->create(['is_active' => 1]);
        $programme = Programme::factory()->create(['group_programme_id' => $category->id, 'is_active' => 1]);
        $emission  = Emision::factory()->create([
            'programme_id' => $programme->id,
            'media_type'   => Emision::TYPE_AUDIO,
            'is_active'    => true,
            'active_at'    => now()->subDay(),
        ]);
        $emission->attachment()->attach($this->legacyAttachment());

        $url = $emission->fresh()->audioUrl();

        $this->assertNotNull($url);
        $this->assertStringEndsWith('old/0/90.mp3', $url, 'L’URL doit pointer le fichier physique');
        $this->assertStringEndsNotWith('.mp3.', $url, 'Plus jamais de point orphelin');
    }

    /** Charge la migration avec un rapport écrit dans un fichier temporaire. */
    private function migration(): object
    {
        $migration = include database_path('migrations/2026_08_09_110000_fix_legacy_attachment_extensions.php');
        $migration->reportPath = $this->reportPath;

        return $migration;
    }

    private string $reportPath;

    protected function tearDown(): void
    {
        @unlink($this->reportPath ?? '');
        parent::tearDown();
    }

    public function test_la_migration_scinde_les_extensions_des_lignes_2020(): void
    {
        $this->reportPath = tempnam(sys_get_temp_dir(), 'rapport-attachments');

        // Ligne 2020 dont le fichier physique EXISTE → réparable.
        $legacy = $this->legacyAttachment();
        Storage::disk('emission_audio')->put('old/0/90.mp3', 'audio');

        $moderne = Attachment::factory()->create([
            'name' => '01KZZP4GGPN4D0HWWH46RQH6XV', 'extension' => 'mp3',
            'original_name' => 'episode.mp3', 'mime' => 'audio/mpeg',
            'path' => '2026/08/', 'disk' => 'emission_audio', 'group' => 'audio',
            'size' => 456, 'sort' => 0,
        ]);
        $sansPoint = $this->legacyAttachment(['name' => 'fichier-sans-extension', 'original_name' => 'x']);

        ob_start();
        $this->migration()->up();
        ob_end_clean();

        // Ligne 2020 : scindée (name sans extension, extension renseignée).
        $legacy->refresh();
        $this->assertSame('90', $legacy->name);
        $this->assertSame('mp3', $legacy->extension);

        // Ligne moderne : intouchée.
        $moderne->refresh();
        $this->assertSame('01KZZP4GGPN4D0HWWH46RQH6XV', $moderne->name);
        $this->assertSame('mp3', $moderne->extension);

        // Sans point dans le nom : intouchée (pas de fausse extension inventée).
        $sansPoint->refresh();
        $this->assertSame('fichier-sans-extension', $sansPoint->name);
        $this->assertNull($sansPoint->extension);

        // Rapport : 1 réparé, 0 échec.
        $report = file_get_contents($this->reportPath);
        $this->assertStringContainsString('Réparés (fichier physique vérifié) : 1', $report);
        $this->assertStringContainsString('Aucun échec.', $report);

        // Idempotente : un second passage ne change plus rien.
        ob_start();
        $this->migration()->up();
        ob_end_clean();
        $this->assertSame('90', $legacy->fresh()->name);
    }

    public function test_fichier_manquant_ligne_intacte_et_listee_au_rapport(): void
    {
        $this->reportPath = tempnam(sys_get_temp_dir(), 'rapport-attachments');

        // Ligne 2020 dont le fichier physique N'EXISTE PAS → intacte + rapportée.
        $orphelin = $this->legacyAttachment(['name' => '404.mp3', 'original_name' => '404.mp3']);

        ob_start();
        $this->migration()->up();
        ob_end_clean();

        $orphelin->refresh();
        $this->assertSame('404.mp3', $orphelin->name, 'Sans fichier physique, la ligne reste intacte');
        $this->assertNull($orphelin->extension);

        $report = file_get_contents($this->reportPath);
        $this->assertStringContainsString('Échecs (fichier introuvable, ligne laissée intacte) : 1', $report);
        $this->assertStringContainsString('#' . $orphelin->id, $report);
        $this->assertStringContainsString('old/0/404.mp3', $report);
    }

    public function test_une_image_2020_est_aussi_reparee(): void
    {
        $this->reportPath = tempnam(sys_get_temp_dir(), 'rapport-attachments');

        $image = $this->legacyAttachment([
            'name' => 'pochette.jpeg', 'original_name' => 'pochette.jpeg',
            'mime' => 'image/*', 'disk' => 'emission_image', 'group' => null,
        ]);
        Storage::disk('emission_image')->put('old/0/pochette.jpeg', 'img');

        ob_start();
        $this->migration()->up();
        ob_end_clean();

        $image->refresh();
        $this->assertSame('pochette', $image->name);
        $this->assertSame('jpeg', $image->extension);
    }
}
