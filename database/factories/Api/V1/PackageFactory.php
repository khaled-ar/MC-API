<?php

namespace Database\Factories\Api\V1;

use App\Models\Api\V1\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\V1\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => '1', //Admin::all()->random()->admin_id,
            'name' => 'الباقة الإفتراضية', //fake()->unique()->words(3, true),
            'description' => 'باقة إفتراضية تمنح عند إنشاء حساب جديد', //fake()->text(),
            'cost' => 0, //fake()->numberBetween(1, 999),
            'validity' => fake()->numberBetween(1, 999),
            'type' => 'public', //fake()->randomElement(['public', 'private']),
            'discount' => false,
            'sale_ads_validity' => fake()->numberBetween(1, 999),
            'sale_ads_limit' => fake()->numberBetween(1, 999),
            'sale_ads_updateable' => fake()->boolean(),
            'sale_ads_resultable' => fake()->boolean(),
            'buy_ads_validity' => fake()->numberBetween(1, 999),
            'buy_ads_limit' => fake()->numberBetween(1, 999),
            'buy_ads_updateable' => fake()->boolean(),
            'buy_ads_resultable' => fake()->boolean(),
            'offers_limit' => fake()->numberBetween(1, 999),
            'service_discounts' => fake()->numberBetween(1, 999),
            'hide_offer' => fake()->boolean(),
            'offer_highlighting' => fake()->boolean(),
            'pinable' => fake()->boolean(),
            'pinable_validity' => fake()->numberBetween(1, 999),
        ];
    }
}
