<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Admin;
use App\Models\Api\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => '1', //User::all()->random()->id,
            'admin_id' => '1', //Admin::all()->random()->admin_id,
            'package_id' => '1',
            'amount' => 0// fake()->randomFloat(2, 0, 100000)
        ];
    }
}
