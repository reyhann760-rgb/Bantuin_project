<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ServiceOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceOffer>
 */
class ServiceOfferFactory extends Factory
{
    protected $model = ServiceOffer::class;

    public function definition(): array
    {
        return [
            'provider_id' => User::factory()->provider(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'price' => fake()->optional()->numberBetween(50000, 2000000),
            'price_type' => fake()->randomElement(['fixed', 'hourly', 'negotiable']),
            'work_mode' => fake()->randomElement(['online', 'onsite', 'hybrid']),
            'location' => fake()->city(),
            'portfolio_url' => null,
            'image' => null,
            'status' => 'active',
        ];
    }
}