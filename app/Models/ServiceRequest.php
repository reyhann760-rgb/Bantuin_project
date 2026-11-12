<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    protected $fillable = [
        'title',
        'description',
        'budget_min',
        'budget_max',
        'work_mode',
        'location',
        'deadline',
    ];

    // Relasi dengan Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relasi dengan percakapan
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'service_request_id');
    }
}