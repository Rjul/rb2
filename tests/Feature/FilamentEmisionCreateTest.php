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
}
