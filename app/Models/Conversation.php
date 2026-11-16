<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'provider_id',
        'service_request_id',
        'service_offer_id',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    // Relasi dengan Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relasi dengan Penyedia Jasa
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    // Relasi dengan kebutuhan jasa
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    // Relasi dengan penawaran jasa
    public function serviceOffer(): BelongsTo
    {
        return $this->belongsTo(ServiceOffer::class, 'service_offer_id');
    }

    // Relasi dengan pesan
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'conversation_id');
    }

    /**
     * Check if a given user is a participant of this conversation.
     */
    public function hasParticipant(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : (int) $user;
        return $this->customer_id === $userId || $this->provider_id === $userId;
    }

    /**
     * Get the other participant in this conversation.
     */
    public function getOtherParticipant(User|int $user): ?User
    {
        $userId = $user instanceof User ? $user->id : (int) $user;

        if ($this->customer_id === $userId) {
            return $this->provider;
        }

        if ($this->provider_id === $userId) {
            return $this->customer;
        }

        return null;
    }

    /**
     * Check if this conversation has been completed.
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Check if a user has reviewed this conversation.
     */
    public function hasReviewedBy(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : (int) $user;
        return $this->reviews()->where('reviewer_id', $userId)->exists();
    }
}
