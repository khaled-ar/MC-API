<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => Admin::all()->random()->admin_id,
            'name' => fake()->unique()->name(),
            'description' => fake()->text(),
            'image' => fake()->imageUrl(),
            'status' => fake()->randomElement(['active', 'inactive', 'archived']),
        ];
    }
}
