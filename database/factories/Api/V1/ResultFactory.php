<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ads = Ad::pluck('id');
        return [
            'ad_id' => fake()->unique()->randomElement($ads),
            'user_id' => User::all()->random()->id,
            'search_count' => fake()->numberBetween(0, 100000),
            'view_count' => fake()->numberBetween(0, 100000),
            'share_count' => fake()->numberBetween(0, 100000),
            'favorited_count' => fake()->numberBetween(0, 100000),
            'call_click_count' => fake()->numberBetween(0, 100000),
            'whatsapp_click_count' => fake()->numberBetween(0, 100000),
            'messages_click_count' => fake()->numberBetween(0, 100000),
        ];
    }
}
