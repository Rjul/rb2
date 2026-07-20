<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * Rendu du contenu administrable (descriptions RichEditor) sur le front via
 * <x-tall.rich-text>. La mise en forme du RichEditor Filament (soulignement,
 * couleur, alignement) est portée par des styles inline → doit être préservée ;
 * les vecteurs dangereux (script, on*) doivent rester supprimés.
 */
class RichTextRenderTest extends TestCase
{
    private function render(string $html): string
    {
        return Blade::render('<x-tall.rich-text :html="$html" />', ['html' => $html]);
    }

    public function test_le_soulignement_inline_est_preserve(): void
    {
        $out = $this->render('<p><span style="text-decoration:underline">souligné</span></p>');

        $this->assertStringContainsString('text-decoration:underline', $out);
        $this->assertStringContainsString('souligné', $out);
    }

    public function test_les_mises_en_forme_courantes_passent(): void
    {
        $out = $this->render('<h2>t</h2><p style="text-align:center"><strong>g</strong><em>i</em><s>b</s><span style="color:rgb(30,101,65)">c</span></p>');

        $this->assertStringContainsString('<strong>', $out);
        $this->assertStringContainsString('<em>', $out);
        $this->assertStringContainsString('text-align:center', $out);
        $this->assertStringContainsString('color:rgb(30,101,65)', $out);
    }

    public function test_les_scripts_et_handlers_restent_bloques(): void
    {
        $out = $this->render('<p onclick="alert(1)">ok</p><script>alert(2)</script>');

        $this->assertStringNotContainsString('<script', $out);
        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringContainsString('ok', $out);
    }
}
