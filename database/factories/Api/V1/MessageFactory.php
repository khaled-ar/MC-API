<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Message>
 */
class MessageFactory extends Factory
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
            'fname' => explode(" ", fake()->name())[0],
            'lname' => explode(" ", fake()->name())[1],
            'phone_number' => fake()->phoneNumber(),
            'content' => fake()->sentences(3, true),
            'read_at' => fake()->randomElement([null, now()]),
        ];
    }
}
