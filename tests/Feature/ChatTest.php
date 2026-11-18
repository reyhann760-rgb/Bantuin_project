<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $provider;
    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->provider = User::factory()->create([
            'role' => 'provider',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->category = Category::factory()->create();
    }

    public function test_provider_can_start_conversation_from_open_service_request(): void
    {
        $request = ServiceRequest::factory()->create([
            'customer_id' => $this->customer->id,
            'category_id' => $this->category->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->provider)
            ->post(route('chat.store'), [
                'service_request_id' => $request->id,
            ]);

        $this->assertDatabaseHas('conversations', [
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'service_request_id' => $request->id,
        ]);

        $conversation = Conversation::first();
        $response->assertRedirect(route('chat.show', $conversation));
    }

    public function test_customer_can_start_conversation_from_active_service_offer(): void
    {
        $offer = ServiceOffer::factory()->create([
            'provider_id' => $this->provider->id,
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('chat.store'), [
                'service_offer_id' => $offer->id,
            ]);

        $this->assertDatabaseHas('conversations', [
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'service_offer_id' => $offer->id,
        ]);

        $conversation = Conversation::first();
        $response->assertRedirect(route('chat.show', $conversation));
    }

    public function test_admin_cannot_start_conversation(): void
    {
        $request = ServiceRequest::factory()->create([
            'customer_id' => $this->customer->id,
            'category_id' => $this->category->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('chat.store'), [
                'service_request_id' => $request->id,
            ]);

        $response->assertSessionHasErrors(['user']);
        $this->assertDatabaseCount('conversations', 0);
    }

    public function test_starting_conversation_again_does_not_create_duplicate(): void
    {
        $request = ServiceRequest::factory()->create([
            'customer_id' => $this->customer->id,
            'category_id' => $this->category->id,
            'status' => 'open',
        ]);

        $this->actingAs($this->provider)->post(route('chat.store'), [
            'service_request_id' => $request->id,
        ]);

        $this->actingAs($this->provider)->post(route('chat.store'), [
            'service_request_id' => $request->id,
        ]);

        $this->assertDatabaseCount('conversations', 1);
    }

    public function test_participants_can_send_and_view_messages(): void
    {
        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
        ]);

        // Send as provider
        $response = $this->actingAs($this->provider)
            ->post(route('chat.send', $conversation), [
                'body' => 'Halo, saya tertarik dengan tawaran ini.',
            ]);

        $response->assertRedirect(route('chat.show', $conversation));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Halo, saya tertarik dengan tawaran ini.',
        ]);

        // Customer receives database notification
        $this->assertCount(1, $this->customer->notifications);

        // Polling messages endpoint
        $pollResponse = $this->actingAs($this->customer)
            ->getJson(route('chat.messages', $conversation));

        $pollResponse->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonFragment([
                'body' => 'Halo, saya tertarik dengan tawaran ini.',
            ]);
    }

    public function test_non_participant_cannot_access_or_send_messages(): void
    {
        $thirdUser = User::factory()->create(['role' => 'customer']);

        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
        ]);

        // Try viewing chat
        $this->actingAs($thirdUser)
            ->get(route('chat.show', $conversation))
            ->assertForbidden();

        // Try sending message
        $this->actingAs($thirdUser)
            ->post(route('chat.send', $conversation), ['body' => 'Intruder message'])
            ->assertForbidden();
    }

    public function test_customer_can_mark_conversation_completed_and_closes_service_request(): void
    {
        $request = ServiceRequest::factory()->create([
            'customer_id' => $this->customer->id,
            'category_id' => $this->category->id,
            'status' => 'open',
        ]);

        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'service_request_id' => $request->id,
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('chat.complete', $conversation));

        $response->assertRedirect(route('chat.show', $conversation));

        $conversation->refresh();
        $this->assertNotNull($conversation->completed_at);
        $this->assertEquals('completed', $conversation->status);

        $request->refresh();
        $this->assertEquals('closed', $request->status);
    }
}
