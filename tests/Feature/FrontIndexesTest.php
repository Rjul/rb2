<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Les index de performance du front (redirections + filtre/tri publié) sont bien
 * présents après migration. Sans eux, chaque requête front et chaque 301 fait un
 * scan de table — critique au recrawl post-bascule.
 */
class FrontIndexesTest extends TestCase
{
    use RefreshDatabase;

    /** @return string[] */
    private function indexNames(string $table): array
    {
        return array_map(fn ($i) => $i['name'], Schema::getIndexes($table));
    }

    public function test_les_index_de_performance_existent(): void
    {
        $emisions = $this->indexNames('emisions');
        $this->assertContains('emisions_active_idx', $emisions);
        $this->assertContains('emisions_slug_idx', $emisions);
        $this->assertContains('emisions_media_active_idx', $emisions);

        $programmes = $this->indexNames('programmes');
        $this->assertContains('programmes_slug_idx', $programmes);
        $this->assertContains('programmes_active_height_idx', $programmes);

        $this->assertContains('group_programmes_active_height_idx', $this->indexNames('group_programmes'));
    }
}
