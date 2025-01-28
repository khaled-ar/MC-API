<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Admin;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Comment>
 */
class CommentFactory extends Factory
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
            'approved_by' => Admin::all()->random()->admin_id,
            'content' => fake()->text(),
        ];
    }
}
