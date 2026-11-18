<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Hanya peserta percakapan (customer atau provider) yang boleh melihat percakapan.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->hasParticipant($user);
    }

    /**
     * Hanya peserta percakapan yang boleh mengirim pesan.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $conversation->hasParticipant($user);
    }

    /**
     * Hanya customer dalam percakapan yang boleh menandai selesai.
     */
    public function complete(User $user, Conversation $conversation): bool
    {
        return $conversation->customer_id === $user->id && !$conversation->isCompleted();
    }

    /**
     * Hanya customer yang boleh memberikan ulasan setelah selesai.
     */
    public function review(User $user, Conversation $conversation): bool
    {
        return $conversation->customer_id === $user->id
            && $conversation->isCompleted()
            && !$conversation->hasReviewedBy($user);
    }
}

