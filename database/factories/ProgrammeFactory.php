<?php

namespace Database\Factories;

use App\Models\GroupProgramme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Programme>
 */
class ProgrammeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->sentence(3, false),
            'user_id' => User::all()->random()->id,
            'group_programme_id' => GroupProgramme::all()->random()->id,
            'description' => fake()->realText(200),
            'image' => fake()->imageUrl(800, 533),
            'active' => fake()->boolean(95),
            'is_archived' => fake()->boolean(5),
            'height' => fake()->numberBetween(1, 20),
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
