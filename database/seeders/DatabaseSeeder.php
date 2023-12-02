<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Psy\Util\Str;
use App\Models\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::factory()->create([
             'name' => 'Administrateur',
             'email' => 'admin@admin.com',
             'password' => bcrypt('password'),
             'email_verified_at' => (new \DateTime('now'))->getTimestamp(),
             'permissions' => ["platform.index"=> true, "platform.programmes"=> true, "platform.systems.roles"=> true, "platform.systems.users"=> true, "platform.group.programme"=> true, "platform.systems.attachment"=> true],
         ]);

        if ($this->command->confirm('Install tests data?')) {

             Attachment::factory()->createMany([
                [
                    'name' => '0c18a1f269e38d3a98e9bb31a87bf4b7c6bade47',
                    'original_name' => "Shaka Ponk - Im Picky [OFFICIAL VIDEOCLIP].mp4",
                    "mime" => "video/mp4",
                    "extension" => "mp4",
                    "size" => "3942353",
                    "path" => "2023/07/08/",
                    "hash" => "cb9ca73f827b93d0f726e0a834638cce5a3bf786",
                    "disk" => "emission_video",
                    "user_id" => 1,
                    "group" => "video",
                    "sort" => 0
                ],[
                    'name' => 'bf444c3183abb9e9282524a4ce5eb06e927d90e5',
                    'original_name' => "GAZO-DIE.mp3",
                    "mime" => "audio/mpeg",
                    "extension" => "mp3",
                    "size" => "5593317",
                    "path" => "2022/12/27/",
                    "hash" => "c87817f520f1d1192a83bd627d0fb0d42892bdc3",
                    "disk" => "emission_audio",
                    "user_id" => 1,
                    "group" => "audio",
                    "sort" => 0
                ],
             ]);
             GroupProgramme::factory()->createMany([
                 [
                     'name' => 'Chroniques',
                     'description' => fake('fr_FR')->realText(200),
                     'image' => 'https://picsum.photos/800/533',
                     'is_active' => 1,
                     'height' => 1,
                 ],[
                     'name' => 'Magasines',
                     'description' => fake('fr_FR')->realText(200),
                     'image' => 'https://picsum.photos/800/533',
                     'is_active' => 1,
                     'height' => 2,
                 ],[
                     'name' => 'Culture',
                     'description' => fake('fr_FR')->realText(200),
                     'image' => 'https://picsum.photos/800/533',
                     'is_active' => 1,
                     'height' => 3,
                 ],[
                     'name' => 'Musical',
                     'description' => fake('fr_FR')->realText(200),
                     'image' => 'https://picsum.photos/800/533',
                     'is_active' => 1,
                     'height' => 4,
                 ]
             ]);

            User::factory(100)->create();
            Tag::factory(20 )->create();

            Programme::factory(40)->create();
            $variableALaCon = [
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                ];
            foreach($variableALaCon as $hehe) {
                $attachment = Attachment::all()->random();
                $type = $attachment->mime === 'audio/mpeg' ? 'audio' : 'video';
                Emision::factory(10)
                    ->hasAttached(
                        Tag::all()->random(5) ,
                    )
                    ->hasAttached($attachment)
                    ->create(['media_type' => $type]);
            }
            $variableALaCon = [
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9,
                1,2,3,4,5,6,7,8,9
                ];
            foreach($variableALaCon as $hehe) {
                $type = 'text';
                Emision::factory(10)
                    ->hasAttached(
                        Tag::all()->random(5) ,
                    )
                    ->create(['media_type' => $type]);
            }
            Comment::factory(300)
                ->state(new Sequence(
                    fn ($sequence) => [
                        'commentable_type'  => 'App\Models\Emision',
                        'commentable_id'    => Emision::all()->random()->id,
                        'commenter_type'    => 'App\Models\User',
                        'commenter_id'      => User::all()->random()->id,
                    ]))
                ->create();
        }



    }
}
