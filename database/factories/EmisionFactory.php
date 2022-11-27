<?php

namespace Database\Factories;

use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Emision>
 */
class EmisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake('fr_FR')->unique()->name,
            'user_id' => User::all()->random()->id,
            'programme_id' => Programme::all()->random()->id,
            'description' => fake('fr_FR')->realText(200),
            'media_type' => fake('fr_FR')->randomElement(['text', 'audio', 'video']),
            'is_put_forward' => fake('fr_FR')->boolean(4),
            'image' => fake()->imageUrl(800, 533),
            'active' => fake()->boolean(95),
            'active_at' => fake()->dateTimeAD(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
