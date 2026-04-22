<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Factories\HasFactory};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model {
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'booking_id', 'amount', 'currency',
        'gateway', 'method', 'status', 'gateway_payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_metadata' => 'array',
    ];

    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }

    public function scopeSuccessful($query) { return $query->where('status', 'succeeded'); }
    public function scopeFailed($query) { return $query->where('status', 'failed'); }
}
