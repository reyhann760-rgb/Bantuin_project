<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'customer_id',
        'provider_id',
        'service_request_id',
        'service_offer_id',
        'status',
    ];

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
}