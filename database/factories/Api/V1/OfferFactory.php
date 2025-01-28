<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'ad_id' => Ad::all()->random()->id,
            'type' => fake()->randomElement(['visible', 'hidden']),
            'status' => fake()->randomElement(['active', 'inactive', 'pending', 'unaccept']),
            'offer_highlighting' => fake()->boolean(),
            'content' => fake()->text(),
            'value' => fake()->randomFloat(2, 0, 100000),
        ];
    }
}
