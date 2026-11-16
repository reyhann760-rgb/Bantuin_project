<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
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
        ]);
    }

    public function test_user_receives_database_notification_when_message_is_sent(): void
    {
        // Provider sends message to customer
        $this->actingAs($this->provider)
            ->post(route('chat.send', $this->conversation), [
                'body' => 'Halo Customer, ini notifikasi uji.',
            ]);

        $this->assertCount(1, $this->customer->unreadNotifications);
        $notification = $this->customer->unreadNotifications->first();

        $this->assertEquals(NewMessage::class, $notification->type);
        $this->assertEquals($this->conversation->id, $notification->data['conversation_id']);
        $this->assertEquals('Halo Customer, ini notifikasi uji.', $notification->data['snippet']);
    }

    public function test_user_can_view_notification_list(): void
    {
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Pesan uji untuk notifikasi.',
        ]);

        $this->customer->notify(new NewMessage($message));

        $response = $this->actingAs($this->customer)
            ->get(route('notifications.index'));

        $response->assertOk()
            ->assertSee('Pesan uji untuk notifikasi.')
            ->assertSee($this->provider->name);
    }

    public function test_user_clicking_notification_marks_as_read_and_redirects_to_chat(): void
    {
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Pesan untuk dicek.',
        ]);

        $this->customer->notify(new NewMessage($message));
        $notification = $this->customer->unreadNotifications->first();

        $this->assertTrue($notification->unread());

        $response = $this->actingAs($this->customer)
            ->get(route('notifications.show', $notification->id));

        $response->assertRedirect(route('chat.show', $this->conversation));

        $notification->refresh();
        $this->assertFalse($notification->unread());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $msg1 = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Pesan 1',
        ]);
        $msg2 = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Pesan 2',
        ]);

        $this->customer->notify(new NewMessage($msg1));
        $this->customer->notify(new NewMessage($msg2));

        $this->assertEquals(2, $this->customer->unreadNotifications()->count());

        $response = $this->actingAs($this->customer)
            ->post(route('notifications.read-all'));

        $response->assertRedirect(route('notifications.index'));
        $this->assertEquals(0, $this->customer->unreadNotifications()->count());
    }

    public function test_user_cannot_view_or_mark_other_users_notification(): void
    {
        $stranger = User::factory()->create(['role' => 'customer']);

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Pesan privat.',
        ]);

        $this->customer->notify(new NewMessage($message));
        $notification = $this->customer->unreadNotifications->first();

        $this->actingAs($stranger)
            ->get(route('notifications.show', $notification->id))
            ->assertNotFound();
    }
}

