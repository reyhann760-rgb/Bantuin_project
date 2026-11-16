<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $provider;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'password' => bcrypt('password123'),
        ]);

        $this->provider = User::factory()->create([
            'role' => 'provider',
            'password' => bcrypt('password123'),
        ]);

        $this->category = Category::factory()->create();
    }

    public function test_user_can_login_and_receive_sanctum_token(): void
    {
        $response = $this->postJson(route('api.login'), [
            'email' => $this->customer->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_authenticated_user_can_access_me_endpoint(): void
    {
        $token = $this->customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('api.me'));

        $response->assertOk()
            ->assertJsonPath('user.email', $this->customer->email);
    }

    public function test_user_can_logout_and_revoke_token(): void
    {
        $token = $this->customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.logout'));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertCount(0, $this->customer->tokens);
    }

    public function test_can_browse_service_requests_and_offers_via_api(): void
    {
        ServiceRequest::factory()->create([
            'customer_id' => $this->customer->id,
            'category_id' => $this->category->id,
            'title' => 'Permintaan API',
            'status' => 'open',
        ]);

        ServiceOffer::factory()->create([
            'provider_id' => $this->provider->id,
            'category_id' => $this->category->id,
            'title' => 'Penawaran API',
            'status' => 'active',
        ]);

        $resReq = $this->getJson(route('api.requests.index'));
        $resReq->assertOk()
            ->assertJsonFragment(['title' => 'Permintaan API']);

        $resOffer = $this->getJson(route('api.offers.index'));
        $resOffer->assertOk()
            ->assertJsonFragment(['title' => 'Penawaran API']);
    }

    public function test_user_can_start_conversation_and_send_messages_via_api(): void
    {
        $offer = ServiceOffer::factory()->create([
            'provider_id' => $this->provider->id,
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        $customerToken = $this->customer->createToken('cust-token')->plainTextToken;

        // Customer starts conversation
        $resStart = $this->withHeader('Authorization', 'Bearer ' . $customerToken)
            ->postJson(route('api.conversations.store'), [
                'service_offer_id' => $offer->id,
            ]);

        $resStart->assertCreated()
            ->assertJsonPath('data.service_offer_id', $offer->id);

        $conversationId = $resStart->json('data.id');

        // Customer sends message
        $resMsg = $this->withHeader('Authorization', 'Bearer ' . $customerToken)
            ->postJson(route('api.conversations.send', $conversationId), [
                'body' => 'Halo penyedia jasa via API',
            ]);

        $resMsg->assertCreated()
            ->assertJsonPath('data.body', 'Halo penyedia jasa via API');

        // Check messages list
        $resList = $this->withHeader('Authorization', 'Bearer ' . $customerToken)
            ->getJson(route('api.conversations.messages', $conversationId));

        $resList->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.body', 'Halo penyedia jasa via API');
    }
}

