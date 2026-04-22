<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferTier extends Model
{
    protected $fillable = [
        'offer_id',
        'type',
        'label',
        'tagline',
        'price',
        'price_is_indicative',
        'currency',
        'description',
        'included_items',
        'excluded_items',
        'whatsapp_only',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'included_items'      => 'array',
        'excluded_items'      => 'array',
        'price'               => 'decimal:2',
        'price_is_indicative' => 'boolean',
        'whatsapp_only'       => 'boolean',
        'is_active'           => 'boolean',
    ];

    public const TYPES = [
        'discovery' => 'Découverte',
        'comfort'   => 'Confort',
        'exception' => 'Exception',
    ];

    public const TAGLINES = [
        'discovery' => "L'essentiel de l'expérience",
        'comfort'   => 'Transport, repas et confort inclus',
        'exception' => 'Expérience VIP entièrement personnalisée',
    ];

    // ════════════════════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════════════════════

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS
    // ════════════════════════════════════════════════════════

    public function getEmojiAttribute(): string
    {
        return match ($this->type) {
            'discovery' => '🌍',
            'comfort'   => '⭐',
            'exception' => '💎',
            default     => '✨',
        };
    }

    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'discovery' => 'var(--sienna-v, #8B5E3C)',
            'comfort'   => 'var(--copper, #C1440E)',
            'exception' => '#b59a6e',
            default     => 'var(--copper)',
        };
    }

    /** Prix formaté FCFA */
    public function getPriceFormattedAttribute(): string
    {
        $prefix = $this->price_is_indicative ? 'À partir de ' : '';
        return $prefix . number_format((float) $this->price, 0, '', ' ') . ' FCFA';
    }

    /**
     * Prix indicatif en euros.
     * CORRECTION : utiliser config() au lieu de 655.957 hardcodé.
     */
    public function getPriceEurAttribute(): string
    {
        $eurRate = config('discovtrip.eur_rate', 655.957);
        $euros   = number_format((float) $this->price / $eurRate, 0, '', ' ');
        return '≈ ' . $euros . ' €';
    }
}