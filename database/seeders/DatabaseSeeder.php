<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * L'administrateur est toujours créé. Le jeu de données de démonstration est
     * peuplé automatiquement HORS production (local / CI) — sans confirmation
     * interactive, pour que `php artisan migrate:fresh --seed --force` fonctionne.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'permissions' => User::superAdminPermissions(),
        ]);

        // Sécurité : jamais de fausses données en production.
        if (app()->environment('production')) {
            return;
        }

        $this->seedDemoData();
    }

    private function seedDemoData(): void
    {
        // Deux pièces jointes réelles (1 vidéo, 1 audio) → alimentent le lecteur.
        Attachment::factory()->createMany([
            [
                'name' => '0c18a1f269e38d3a98e9bb31a87bf4b7c6bade47',
                'original_name' => 'Shaka Ponk - Im Picky [OFFICIAL VIDEOCLIP].mp4',
                'mime' => 'video/mp4', 'extension' => 'mp4', 'size' => '3942353',
                'path' => '2023/07/08/', 'hash' => 'cb9ca73f827b93d0f726e0a834638cce5a3bf786',
                'disk' => 'emission_video', 'user_id' => 1, 'group' => 'video', 'sort' => 0,
            ],
            [
                'name' => 'bf444c3183abb9e9282524a4ce5eb06e927d90e5',
                'original_name' => 'GAZO-DIE.mp3',
                'mime' => 'audio/mpeg', 'extension' => 'mp3', 'size' => '5593317',
                'path' => '2022/12/27/', 'hash' => 'c87817f520f1d1192a83bd627d0fb0d42892bdc3',
                'disk' => 'emission_audio', 'user_id' => 1, 'group' => 'audio', 'sort' => 0,
            ],
        ]);

        // 4 catégories actives, triées par poids (1 → 4).
        GroupProgramme::factory()->createMany([
            ['name' => 'Chroniques', 'description' => fake('fr_FR')->realText(200), 'image' => 'https://picsum.photos/800/533', 'is_active' => 1, 'height' => 1],
            ['name' => 'Magasines',  'description' => fake('fr_FR')->realText(200), 'image' => 'https://picsum.photos/800/533', 'is_active' => 1, 'height' => 2],
            ['name' => 'Culture',    'description' => fake('fr_FR')->realText(200), 'image' => 'https://picsum.photos/800/533', 'is_active' => 1, 'height' => 3],
            ['name' => 'Musical',    'description' => fake('fr_FR')->realText(200), 'image' => 'https://picsum.photos/800/533', 'is_active' => 1, 'height' => 4],
        ]);

        User::factory(100)->create();
        Tag::factory(20)->create();
        Programme::factory(40)->create();

        $audio = Attachment::where('group', 'audio')->first();

        // ~90 émissions audio/vidéo (fichier joint aléatoire → type déduit).
        foreach (range(1, 90) as $i) {
            $attachment = Attachment::all()->random();
            $type = $attachment->mime === 'audio/mpeg' ? 'audio' : 'video';
            Emision::factory()
                ->hasAttached(Tag::all()->random(5))
                ->hasAttached($attachment)
                ->create(['media_type' => $type]);
        }

        // ~27 articles (texte, sans pièce jointe).
        foreach (range(1, 27) as $i) {
            Emision::factory()
                ->hasAttached(Tag::all()->random(5))
                ->create(['media_type' => Emision::TYPE_TEXT]);
        }

        // 6 émissions « à la une » publiées CETTE SEMAINE (pour l'accueil / la newsletter).
        foreach (range(1, 6) as $i) {
            Emision::factory()
                ->hasAttached(Tag::all()->random(3))
                ->hasAttached($audio)
                ->create([
                    'media_type'     => Emision::TYPE_AUDIO,
                    'is_active'      => true,
                    'is_put_forward' => true,
                    'active_at'      => fake()->dateTimeBetween('-6 days', 'now'),
                ]);
        }

        // 300 commentaires (≈95 % approuvés via la factory → visibles sur le front).
        Comment::factory(300)
            ->state(new Sequence(fn () => [
                'commentable_type' => Emision::class,
                'commentable_id'   => Emision::all()->random()->id,
                'commenter_type'   => User::class,
                'commenter_id'     => User::all()->random()->id,
            ]))
            ->create();

        // Pages éditoriales (mêmes paths qu'en prod : liées depuis le footer v2).
        foreach ([
            ['path' => 'l-association',          'name' => 'L’association'],
            ['path' => 'responsabilité-legale',  'name' => 'La responsabilité légale'],
            ['path' => 'protection-des-donnees', 'name' => 'Protection des données'],
        ] as $page) {
            \App\Models\PageAdmin::create($page + [
                'content' => '<h1>' . $page['name'] . '</h1><p>' . fake('fr_FR')->paragraph(6) . '</p><h2>' . fake('fr_FR')->sentence(3) . '</h2><p>' . fake('fr_FR')->paragraph(4) . '</p>',
            ]);
        }
    }
}
