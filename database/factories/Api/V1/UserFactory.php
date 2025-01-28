<?php

namespace Database\Factories\Api\V1;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->name(),
            'fullname' => fake()->name(),
            'phone_verified_at' => now(), //fake()->randomElement([null, now()]),
            'password' => Hash::make('12345678'),
            'phone_number' => fake()->unique()->phoneNumber(),
            'whatsapp' => fake()->unique()->phoneNumber(),
            'country' => fake()->country(),
            'is_admin' => '1', //fake()->randomElement(['0','1']),
            'status' => 'active'
        ];
    }
}
