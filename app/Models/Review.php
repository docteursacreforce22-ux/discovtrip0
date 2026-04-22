<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Factories\HasFactory};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model {
    use HasFactory;

    protected $fillable = [
        'offer_id', 'user_id', 'booking_id', 'rating', 'comment', 'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'published_at' => 'datetime',
    ];

    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }

    public function scopePublished($query) { return $query->where('status', 'published'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
}
