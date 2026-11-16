<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'reviewer_id' => User::factory()->customer(),
            'reviewee_id' => User::factory()->provider(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->sentence(),
        ];
    }

    public function forConversation(Conversation $conversation): static
    {
        return $this->state(fn (array $attributes) => [
            'conversation_id' => $conversation->id,
            'reviewer_id' => $conversation->customer_id,
            'reviewee_id' => $conversation->provider_id,
        ]);
    }
}