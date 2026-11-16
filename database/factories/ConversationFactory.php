<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\ServiceRequest;
use App\Models\ServiceOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory()->customer(),
            'provider_id' => User::factory()->provider(),
            'service_request_id' => null,
            'service_offer_id' => null,
            'status' => 'active',
            'completed_at' => null,
        ];
    }

    public function forRequest(ServiceRequest $serviceRequest): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => $serviceRequest->customer_id,
            'service_request_id' => $serviceRequest->id,
            'service_offer_id' => null,
        ]);
    }

    public function forOffer(ServiceOffer $serviceOffer): static
    {
        return $this->state(fn (array $attributes) => [
            'provider_id' => $serviceOffer->provider_id,
            'service_request_id' => null,
            'service_offer_id' => $serviceOffer->id,
        ]);
    }
}