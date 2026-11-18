<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $provider;
    protected Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->provider = User::factory()->create(['role' => 'provider']);

        $this->conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function test_customer_can_leave_review_for_completed_conversation(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 5,
                'comment' => 'Pengerjaan sangat cepat dan rapi!',
            ]);

        $response->assertRedirect(route('chat.show', $this->conversation));

        $this->assertDatabaseHas('reviews', [
            'conversation_id' => $this->conversation->id,
            'reviewer_id' => $this->customer->id,
            'reviewee_id' => $this->provider->id,
            'rating' => 5,
            'comment' => 'Pengerjaan sangat cepat dan rapi!',
        ]);
    }

    public function test_cannot_leave_review_if_conversation_is_not_completed(): void
    {
        $uncompletedConversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'completed_at' => null,
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('chat.review', $uncompletedConversation), [
                'rating' => 4,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_provider_cannot_leave_review(): void
    {
        $response = $this->actingAs($this->provider)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 5,
                'comment' => 'Review dari provider',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_non_participant_cannot_leave_review(): void
    {
        $thirdUser = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($thirdUser)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 5,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_customer_cannot_leave_multiple_reviews_for_same_conversation(): void
    {
        // First review
        $this->actingAs($this->customer)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 5,
                'comment' => 'Bagus sekali',
            ]);

        $this->assertDatabaseCount('reviews', 1);

        // Second review attempt
        $response = $this->actingAs($this->customer)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 3,
                'comment' => 'Coba ubah ulasan',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_rating_must_be_integer_between_1_and_5(): void
    {
        // Rating out of range (0)
        $response1 = $this->actingAs($this->customer)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 0,
            ]);
        $response1->assertSessionHasErrors('rating');

        // Rating out of range (6)
        $response2 = $this->actingAs($this->customer)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 6,
            ]);
        $response2->assertSessionHasErrors('rating');

        // Valid rating
        $response3 = $this->actingAs($this->customer)
            ->post(route('chat.review', $this->conversation), [
                'rating' => 4,
            ]);
        $response3->assertSessionHasNoErrors();
        $this->assertDatabaseHas('reviews', ['rating' => 4]);
    }
}

