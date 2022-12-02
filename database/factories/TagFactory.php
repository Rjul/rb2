<?php

namespace Database\Factories;

use App\Models\GroupProgramme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Emision>
 */
class TagFactory extends Factory
{

    public function definition()
    {
        return [
            'name' => ['en' => fake('fr_FR')->unique()->realText(12) ],
            'color' => fake()->hexColor(),
            'description' => fake('fr_FR')->realText(200)
        ];
    }

}
