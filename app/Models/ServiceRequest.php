<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'budget_min',
        'budget_max',
        'work_mode',
        'location',
        'deadline',
        'image',
    ];

    // Relasi dengan Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi dengan percakapan
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'service_request_id');
    }
}