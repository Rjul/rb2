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
            'name' => ['fr' => fake('fr_FR')->unique()->realText(20) ],
            'color' => fake()->randomElement(['#471b4c', '#762f51', '#c4816e', '#9d8c7c', '#c8b58a', '#866a67', '#9a9385', '#c5bfa7', '#d9be90', '#4e5560']),
            'description' => fake('fr_FR')->realText(200)
        ];
    }

}
