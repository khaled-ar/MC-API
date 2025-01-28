<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $users = User::pluck('id');
        return [
            'user_id' => '1', //fake()->unique()->randomElement($users),
            'package_id' => '1',
            'sale_ads_validity' => fake()->numberBetween(1, 20),
            'buy_ads_validity' => fake()->numberBetween(1, 20),
            'sale_ads_limit' => fake()->numberBetween(1, 20),
            'buy_ads_limit' => fake()->numberBetween(1, 20),
            'offers_limit' => fake()->numberBetween(1, 20),
            'service_discounts' => fake()->numberBetween(1, 20),
        ];
    }
}
