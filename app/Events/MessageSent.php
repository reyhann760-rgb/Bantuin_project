<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $messageId;
    public int $conversationId;

    public function __construct(Message $message)
    {
        // Per PRD: event hanya membawa ID saja, data diambil lewat endpoint berizin
        $this->messageId = $message->id;
        $this->conversationId = $message->conversation_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }
}

