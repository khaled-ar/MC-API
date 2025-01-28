<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Role;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AdminFactory extends Factory
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
            'admin_id' => fake()->unique()->randomElement($users),
            'role_id' => Role::all()->random()->id
        ];
    }
}
