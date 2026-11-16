<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Review;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_prd_domain_tables_and_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('categories'));
        $this->assertTrue(Schema::hasTable('reviews'));
        $this->assertTrue(Schema::hasTable('notifications'));
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
        $this->assertTrue(Schema::hasColumns('users', ['role', 'phone', 'bio', 'city', 'avatar']));
        $this->assertTrue(Schema::hasColumns('service_requests', ['category_id', 'image', 'status']));
        $this->assertTrue(Schema::hasColumns('service_offers', ['category_id', 'image', 'status']));
        $this->assertTrue(Schema::hasColumns('conversations', ['completed_at', 'service_request_id', 'service_offer_id']));
        $this->assertTrue(Schema::hasColumns('messages', ['conversation_id', 'sender_id', 'body', 'read_at']));
    }

    public function test_model_relations_and_foreign_key_delete_rules_work(): void
    {
        $category = Category::factory()->create();
        $customer = User::factory()->customer()->create();
        $provider = User::factory()->provider()->create();

        $serviceRequest = ServiceRequest::factory()->create([
            'customer_id' => $customer->id,
            'category_id' => $category->id,
        ]);
        $serviceOffer = ServiceOffer::factory()->create([
            'provider_id' => $provider->id,
            'category_id' => $category->id,
        ]);
        $conversation = Conversation::factory()->forRequest($serviceRequest)->create([
            'provider_id' => $provider->id,
            'completed_at' => now(),
        ]);
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $provider->id,
        ]);
        $review = Review::factory()->forConversation($conversation)->create();

        $this->assertSame($category->id, $serviceRequest->category->id);
        $this->assertSame($category->id, $serviceOffer->category->id);
        $this->assertSame($customer->id, $conversation->customer->id);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $conversation->completed_at);
        $this->assertSame($provider->id, $message->sender->id);
        $this->assertSame($customer->id, $review->reviewer->id);
        $this->assertSame($provider->id, $review->reviewee->id);

        $serviceRequest->delete();
        $this->assertNull($conversation->fresh()->service_request_id);

        $category->delete();
        $this->assertDatabaseMissing('service_offers', ['id' => $serviceOffer->id]);

        $customer->delete();
        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_database_seeder_is_repeatable(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::query()->where('email', 'admin@bantuin.test')->count());
        $this->assertSame(1, User::query()->where('email', 'customer@bantuin.test')->count());
        $this->assertSame(1, User::query()->where('email', 'provider@bantuin.test')->count());
        $this->assertSame(1, Category::query()->where('slug', 'jasa-rumah-tangga')->count());
        $this->assertSame(1, ServiceRequest::query()->where('title', 'Bantuan bersih-bersih rumah')->count());
        $this->assertSame(1, ServiceOffer::query()->where('title', 'Jasa bersih-bersih rumah')->count());
        $this->assertSame(1, Conversation::query()->count());
        $this->assertSame(1, Message::query()->count());
        $this->assertSame(1, Review::query()->count());
    }
}