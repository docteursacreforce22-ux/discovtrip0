<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'offer_id',
        'offer_tier_id',
        'booking_date',
        'booking_time',
        'status',
        'adults',
        'children',
        'infants',
        'participants',
        'total_price',
        'currency',
        'notes',
        'special_requests',
        'cancellation_policy',
        // Paiement
        'is_paid',
        'paid_at',
        'payment_id',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_transaction_id',
        // Annulation / remboursement
        'cancelled_at',
        'cancellation_reason',
        'refunded_at',
        'refunded_amount',
        // Rappels
        'reminder_sent',
        'reminder_sent_at',
        // Invité (sans compte)
        'guest_first_name',
        'guest_last_name',
        'guest_email',
        'guest_phone',
    ];

    protected $casts = [
        'booking_date'       => 'datetime',
        'adults'             => 'integer',
        'children'           => 'integer',
        'infants'            => 'integer',
        'participants'       => 'integer',
        'total_price'        => 'decimal:2',
        'refunded_amount'    => 'decimal:2',
        'is_paid'            => 'boolean',
        'confirmation_sent'  => 'boolean',
        'reminder_sent'      => 'boolean',
        'paid_at'            => 'datetime',
        'refunded_at'        => 'datetime',
        'cancelled_at'       => 'datetime',
        'reminder_sent_at'   => 'datetime',
        'expires_at'         => 'datetime',
        'participant_details'=> 'array',
    ];

    // ════════════════════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════════════════════

    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function offer(): BelongsTo { return $this->belongsTo(Offer::class); }
    public function tier(): BelongsTo  { return $this->belongsTo(OfferTier::class, 'offer_tier_id'); }
    public function payment(): HasOne  { return $this->hasOne(Payment::class); }
    public function review(): HasOne   { return $this->hasOne(Review::class); }

    // ════════════════════════════════════════════════════════
    // SCOPES
    // ════════════════════════════════════════════════════════

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'processing']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->whereIn('status', ['cancelled_by_user', 'cancelled_by_partner', 'cancelled_by_system']);
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS
    // ════════════════════════════════════════════════════════

    /**
     * Libellé lisible du statut.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'              => 'En attente de paiement',
            'confirmed'            => 'Confirmée',
            'processing'           => 'En cours',
            'completed'            => 'Terminée',
            'cancelled_by_user'    => 'Annulée par vous',
            'cancelled_by_partner' => 'Annulée par le guide',
            'cancelled_by_system'  => 'Annulée automatiquement',
            'refunded'             => 'Remboursée',
            default                => ucfirst($this->status ?? ''),
        };
    }

    /**
     * Couleur CSS/badge associée au statut (pour Filament et les vues).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'                                                 => 'warning',
            'confirmed'                                               => 'success',
            'processing'                                              => 'info',
            'completed'                                               => 'gray',
            'cancelled_by_user', 'cancelled_by_partner',
            'cancelled_by_system'                                     => 'danger',
            'refunded'                                                => 'purple',
            default                                                   => 'gray',
        };
    }

    /**
     * Modifier de statut pour les vues (classes CSS simplifiées).
     */
    public function getStatusModifierAttribute(): string
    {
        return match ($this->status) {
            'confirmed'            => 'confirmed',
            'cancelled_by_user',
            'cancelled_by_partner' => 'cancelled',
            'completed'            => 'completed',
            default                => 'pending',
        };
    }

    // ════════════════════════════════════════════════════════
    // HELPERS MÉTIER
    // ════════════════════════════════════════════════════════

    /**
     * La réservation peut-elle être annulée ?
     * Conditions : statut annulable + date future + dans la fenêtre gratuite.
     */
    public function canCancel(): bool
    {
        if (! in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }

        if (Carbon::parse($this->booking_date)->isPast()) {
            return false;
        }

        $freeHours = config('discovtrip.cancellation_free_hours', 48);
        $hoursLeft = now()->diffInHours(Carbon::parse($this->booking_date));

        return $hoursLeft >= $freeHours;
    }

    /**
     * L'utilisateur peut-il laisser un avis ?
     */
    public function canReview(): bool
    {
        return $this->status === 'completed'
            && Carbon::parse($this->booking_date)->isPast()
            && ! $this->review()->exists();
    }

    /**
     * Nombre total de participants (adults + children + infants ou champ participants).
     */
    public function getTotalParticipantsAttribute(): int
    {
        // Si les champs adultes/enfants/bébés sont renseignés
        if ($this->adults !== null) {
            return (int) $this->adults
                 + (int) ($this->children ?? 0)
                 + (int) ($this->infants ?? 0);
        }
        return (int) ($this->participants ?? 1);
    }

    /**
     * Email du client (connecté ou invité).
     */
    public function getClientEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    /**
     * Nom complet du client (connecté ou invité).
     */
    public function getClientNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return trim(($this->guest_first_name ?? '') . ' ' . ($this->guest_last_name ?? ''))
            ?: 'Client';
    }
}