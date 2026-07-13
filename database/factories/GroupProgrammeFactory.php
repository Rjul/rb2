<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class GroupProgrammeFactory extends Factory
{

    public function definition()
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake('fr_FR')->realText(200),
            'image' => 'https://picsum.photos/800/533',
            'is_active' => fake()->boolean(95),
            'height' => fake()->numberBetween(1, 20),
        ];
    }


}
