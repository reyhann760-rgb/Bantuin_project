<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceOffer extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'price_type',
        'work_mode',
        'location',
        'portfolio_url',
    ];

    // Relasi dengan Penyedia Jasa
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    // Relasi dengan percakapan
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'service_offer_id');
    }
}