<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Spotlight — expérience phare liée à une offre.
 *
 * Les deux CTAs pointent vers offers.show de l'offre liée.
 * Fallback : cta1_url / cta2_url saisis en admin si pas d'offre liée.
 */
class Spotlight extends Model
{
    protected $fillable = [
        'offer_id',
        'title', 'subtitle', 'description',
        'image', 'badge_text', 'badge_icon', 'highlight_word',
        'stat1_value', 'stat1_label',
        'stat2_value', 'stat2_label',
        'stat3_value', 'stat3_label',
        'cta1_label', 'cta1_url',
        'cta2_label', 'cta2_url',
        'is_active', 'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    /* ─── RELATION ─── */

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /* ─── ACCESSORS ─── */

    /**
     * URL commune aux deux CTAs → offers.show
     * Fallback : cta1_url admin.
     */
    public function getOfferUrlAttribute(): ?string
    {
        if ($this->offer?->slug) {
            return route('offers.show', $this->offer->slug);
        }

        return $this->cta1_url ?: $this->cta2_url ?: null;
    }

    public function getCta1LabelResolvedAttribute(): string
    {
        return $this->cta1_label ?: 'Réserver maintenant';
    }

    public function getCta2LabelResolvedAttribute(): string
    {
        return $this->cta2_label ?: 'En savoir plus';
    }

    /* ─── SCOPE ─── */

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}