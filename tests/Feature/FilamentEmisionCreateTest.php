<?php

namespace Tests\Feature;

use App\Filament\Resources\EmisionResource\Pages\CreateEmision;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentEmisionCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_emission_audio_via_filament(): void
    {
        Storage::fake('emission_image');
        Storage::fake('emission_audio');

        $admin = User::factory()->admin()->create();
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $programme = Programme::factory()->create([
            'user_id' => $admin->id, 'group_programme_id' => $group->id, 'is_active' => true,
        ]);
        $tag = Tag::factory()->create();

        $this->actingAs($admin);

        Livewire::test(CreateEmision::class)
            ->fillForm([
                'name' => 'Mon émission de test',
                'media_type' => Emision::TYPE_AUDIO,
                'programme_id' => $programme->id,
                'tags' => [$tag->id],
                'active_at' => now()->toDateString(),
                'duration' => 12.5,
                'is_active' => true,
                'is_put_forward' => false,
                'image' => [UploadedFile::fake()->image('cover.jpg', 800, 533)],
                'audio_upload' => [UploadedFile::fake()->create('episode.mp3', 200, 'audio/mpeg')],
                'description' => '<p>Description de test</p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $emision = Emision::where('name', 'Mon émission de test')->first();
        $this->assertNotNull($emision, "L'émission doit être créée");
        $this->assertSame(Emision::TYPE_AUDIO, $emision->media_type);
        $this->assertNotNull($emision->getRawOriginal('image'), "L'image doit être enregistrée");
        $this->assertTrue($emision->tags()->count() >= 1, 'Le thème doit être attaché');
        $this->assertTrue($emision->attachment('audio')->count() >= 1, "Le fichier audio (Attachment) doit être attaché");
        $this->assertStringEndsWith('-'.$emision->id, $emision->slug, "L'id doit être inclus dans le slug pour garantir son unicité");
    }

    public function test_creation_emission_video_via_filament(): void
    {
        Storage::fake('emission_image');
        Storage::fake('emission_video');

        $admin = User::factory()->admin()->create();
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $programme = Programme::factory()->create([
            'user_id' => $admin->id, 'group_programme_id' => $group->id, 'is_active' => true,
        ]);
        $tag = Tag::factory()->create();

        $this->actingAs($admin);

        Livewire::test(CreateEmision::class)
            ->fillForm([
                'name' => 'Émission vidéo test',
                'media_type' => Emision::TYPE_VIDEO,
                'programme_id' => $programme->id,
                'tags' => [$tag->id],
                'active_at' => now()->toDateString(),
                'duration' => 30,
                'is_active' => true,
                'image' => [UploadedFile::fake()->image('cover.jpg', 800, 533)],
                'video_upload' => [UploadedFile::fake()->create('episode.mp4', 500, 'video/mp4')],
                'description' => '<p>Vidéo</p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $emision = Emision::where('name', 'Émission vidéo test')->first();
        $this->assertNotNull($emision);
        $this->assertSame(Emision::TYPE_VIDEO, $emision->media_type);
        $attachment = $emision->attachment('video')->first();
        $this->assertNotNull($attachment, 'Le fichier vidéo (Attachment) doit être attaché');
        $this->assertSame('emission_video', $attachment->disk);
    }

    /**
     * Régression : à l'édition, l'aperçu de l'image doit se remplir quel que
     * soit le format hérité en base (chemin relatif Filament, chemin complet
     * d'avant 2023-11, URL absolue du Cropper Orchid). L'accessor renvoie une
     * URL alors que FileUpload attend le chemin relatif au disque.
     *
     * @dataProvider formatsImage
     */
    public function test_apercu_image_a_l_edition(string $valeurEnBase, string $cheminDisqueAttendu): void
    {
        Storage::fake('emission_image');
        Storage::disk('emission_image')->put($cheminDisqueAttendu, 'fake-image');

        $admin = User::factory()->admin()->create();
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $programme = Programme::factory()->create([
            'user_id' => $admin->id, 'group_programme_id' => $group->id, 'is_active' => true,
        ]);
        $emision = Emision::factory()->create([
            'programme_id' => $programme->id,
            'user_id'      => $admin->id,
            'image'        => $valeurEnBase,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Filament\Resources\EmisionResource\Pages\EditEmision::class, ['record' => $emision->id])
            ->assertFormSet(fn (array $state) => $this->assertContains(
                $cheminDisqueAttendu,
                array_values((array) $state['image']),
                "Le chemin disque doit être retrouvé depuis « {$valeurEnBase} »",
            ));
    }

    public static function formatsImage(): array
    {
        return [
            'chemin relatif (Filament)'   => ['2024/05/photo.png', '2024/05/photo.png'],
            'chemin complet (avant 2023)' => ['storage/public/emission/images/old/6/photo.jpg', 'old/6/photo.jpg'],
            'URL absolue (Cropper)'       => ['https://www.radiobastides.fr/storage/public/emission/images/2023/11/30/photo.png', '2023/11/30/photo.png'],
        ];
    }
}
