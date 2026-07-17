<?php

namespace Tests\Feature;

use App\Filament\Resources\EmisionResource\Pages\EditEmision;
use App\Filament\Resources\GroupProgrammeResource\Pages\EditGroupProgramme;
use App\Filament\Resources\ProgrammeResource\Pages\EditProgramme;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Images des émissions, programmes et catégories (groupes de programme) dans Filament.
 *
 * Deux garanties :
 *  1. l'accessor HasResolvedImage rend une URL correcte pour les 3 formats hérités ;
 *  2. éditer PUIS enregistrer sans toucher à l'image ne perd ni ne corrompt sa valeur
 *     (la régression signalée : « l'image n'est plus là quand je modifie »).
 */
class FilamentImageFieldTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    /**
     * L'accessor transforme chaque format hérité en URL affichable, sans double préfixe.
     *
     * @dataProvider formatsImage
     */
    public function test_accessor_resout_les_formats(string $valeurEnBase, string $attenduDansUrl): void
    {
        $admin = $this->admin();
        $group = GroupProgramme::factory()->create(['image' => $valeurEnBase]);
        $programme = Programme::factory()->create([
            'user_id' => $admin->id, 'group_programme_id' => $group->id, 'image' => $valeurEnBase,
        ]);
        $emision = Emision::factory()->create([
            'programme_id' => $programme->id, 'user_id' => $admin->id, 'image' => $valeurEnBase,
        ]);

        foreach ([$group, $programme, $emision] as $model) {
            $this->assertStringContainsString($attenduDansUrl, $model->image);
            // Jamais de doublon du dossier de stockage (le bug d'avant 2023-11).
            $this->assertStringNotContainsString('emission/images/storage', $model->image);
        }
    }

    public static function formatsImage(): array
    {
        return [
            // URL absolue du Cropper Orchid → renvoyée telle quelle.
            'URL absolue (Cropper Orchid)' => [
                'https://www.radiobastides.fr/storage/public/emission/images/2023/11/30/x.png',
                'https://www.radiobastides.fr/storage/public/emission/images/2023/11/30/x.png',
            ],
            // Chemin complet racine web → servi tel quel (pas de re-préfixe).
            'chemin complet (avant 2023-11)' => [
                'storage/public/emission/images/old/6/y.jpg',
                'storage/public/emission/images/old/6/y.jpg',
            ],
            // Chemin relatif → préfixé par l'URL du disque emission_image.
            'chemin relatif (nouveau Filament)' => [
                'programmes/z.png',
                'storage/public/emission/images/programmes/z.png',
            ],
        ];
    }

    /**
     * Édition + enregistrement sans remplacer l'image : la valeur brute survit.
     *
     * @dataProvider editionResources
     */
    public function test_edition_preserve_l_image(string $editPage, \Closure $modelFactory): void
    {
        Storage::fake('emission_image');

        $admin = $this->admin();
        $this->actingAs($admin);

        // Une valeur "legacy" typique : URL absolue posée par le Cropper Orchid.
        $legacy = 'https://www.radiobastides.fr/storage/public/emission/images/2022/03/photo.jpg';
        $record = $modelFactory($admin, $legacy);

        Livewire::test($editPage, ['record' => $record->getKey()])
            // À l'hydratation, l'état contient la valeur brute (clé d'aperçu + préservation).
            ->assertFormSet(fn (array $state) => $this->assertContains(
                $legacy,
                array_values((array) $state['image']),
                'La valeur brute doit peupler le champ image à l\'édition',
            ))
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            $legacy,
            $record->fresh()->getRawOriginal('image'),
            'Enregistrer sans changer l\'image ne doit pas la perdre ni la reformater',
        );
    }

    public static function editionResources(): array
    {
        return [
            'émission' => [
                EditEmision::class,
                function (User $admin, string $image) {
                    $group = GroupProgramme::factory()->create();
                    $programme = Programme::factory()->create([
                        'user_id' => $admin->id, 'group_programme_id' => $group->id,
                    ]);

                    $emision = Emision::factory()->create([
                        'programme_id' => $programme->id, 'user_id' => $admin->id, 'image' => $image,
                    ]);
                    // Thème obligatoire dans le formulaire émission.
                    $emision->tags()->attach(\App\Models\Tag::factory()->create());

                    return $emision;
                },
            ],
            'programme' => [
                EditProgramme::class,
                function (User $admin, string $image) {
                    $group = GroupProgramme::factory()->create();

                    return Programme::factory()->create([
                        'user_id' => $admin->id, 'group_programme_id' => $group->id, 'image' => $image,
                    ]);
                },
            ],
            'catégorie' => [
                EditGroupProgramme::class,
                fn (User $admin, string $image) => GroupProgramme::factory()->create(['image' => $image]),
            ],
        ];
    }
}
