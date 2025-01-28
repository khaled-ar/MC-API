<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Rating;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Report>
 */
class ObjectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::pluck('id');
        return [
            'user_id' => fake()->unique()->randomElement($users),
            'rating_id' => Rating::all()->random()->id,
            'reason' => fake()->text()
        ];
    }
}
