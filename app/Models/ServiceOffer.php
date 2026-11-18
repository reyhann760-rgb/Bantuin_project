<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'price',
        'price_type',
        'work_mode',
        'location',
        'portfolio_url',
        'status',
        'image',
    ];

    // Relasi dengan Penyedia Jasa
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi dengan percakapan
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'service_offer_id');
    }
}