<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class MessageSender
{
    /**
     * Send a message within a conversation.
     *
     * @param User $sender
     * @param Conversation $conversation
     * @param string $body
     * @return Message
     * @throws ValidationException
     */
    public function send(User $sender, Conversation $conversation, string $body): Message
    {
        if (!$conversation->hasParticipant($sender)) {
            throw ValidationException::withMessages([
                'user' => ['Anda bukan peserta dalam percakapan ini.'],
            ]);
        }

        $body = trim($body);
        if ($body === '') {
            throw ValidationException::withMessages([
                'body' => ['Pesan tidak boleh kosong.'],
            ]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $body,
        ]);

        // Touch the conversation updated_at so it moves to top of inbox
        $conversation->touch();

        // Send database notification to the other participant
        $recipient = $conversation->getOtherParticipant($sender);
        if ($recipient) {
            // Check if there is already an unread notification for this conversation to prevent flooding
            $hasUnreadNotification = $recipient->unreadNotifications()
                ->where('type', NewMessage::class)
                ->where('data->conversation_id', $conversation->id)
                ->exists();

            if (!$hasUnreadNotification) {
                $recipient->notify(new NewMessage($message));
            }
        }

        // Try broadcasting event (safe fallback if queue/broadcast not setup)
        try {
            event(new MessageSent($message));
        } catch (Throwable $e) {
            Log::warning('Broadcast message sent error: ' . $e->getMessage());
        }

        return $message;
    }
}

