<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Admin;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AdFactory extends Factory
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
            'category_id' => Category::all()->random()->id,
            'approved_by' => Admin::all()->random()->admin_id,
            'title' => fake()->unique()->text(),
            'location' => fake()->country(),
            'description' => fake()->text(),
            'status' => fake()->randomElement(['active', 'pending', 'inactive', 'unaccept']),
            'price' => fake()->randomNumber(4),
            'updateable' => fake()->randomElement(['0', '1']),
            'resultable' => fake()->randomElement(['0', '1']),
            'pinable' => fake()->randomElement(['0', '1']),
            'type' => fake()->randomElement(['sale', 'buy']),
            'images' => fake()->imageUrl(),
        ];
    }
}
