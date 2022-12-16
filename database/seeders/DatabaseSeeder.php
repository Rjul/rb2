<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
         GroupProgramme::factory()->createMany([
             [
                 'name' => 'Chroniques',
                 'description' => fake('fr_FR')->realText(200),
                 'image' => 'https://picsum.photos/800/533',
                 'active' => 1,
                 'height' => 1,
             ],[
                 'name' => 'Magasines',
                 'description' => fake('fr_FR')->realText(200),
                 'image' => 'https://picsum.photos/800/533',
                 'active' => 1,
                 'height' => 2,
             ],[
                 'name' => 'Culture',
                 'description' => fake('fr_FR')->realText(200),
                 'image' => 'https://picsum.photos/800/533',
                 'active' => 1,
                 'height' => 3,
             ],[
                 'name' => 'Musical',
                 'description' => fake('fr_FR')->realText(200),
                 'image' => 'https://picsum.photos/800/533',
                 'active' => 1,
                 'height' => 4,
             ]
         ]);

        if ($this->command->confirm('Install tests data?')) {

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
                Emision::factory(10)
                    ->hasAttached(
                        Tag::all()->random(5) ,
                    )
                    ->create();
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
