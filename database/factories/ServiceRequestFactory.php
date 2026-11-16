<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory()->customer(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'budget_min' => fake()->optional()->numberBetween(50000, 300000),
            'budget_max' => fake()->optional()->numberBetween(300001, 1500000),
            'work_mode' => fake()->randomElement(['online', 'onsite', 'hybrid']),
            'location' => fake()->city(),
            'deadline' => fake()->optional()->dateTimeBetween('+1 day', '+3 months')?->format('Y-m-d'),
            'image' => null,
            'status' => 'open',
        ];
    }
}