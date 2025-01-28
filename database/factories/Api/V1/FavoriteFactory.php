<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::where('status', 'active')->get()->random()->id,
            'ad_id' => Ad::where('status', 'active')->get()->random()->id,
        ];
    }
}
