<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'short_description', 'description', 'long_description',
        'category', 'city_id', 'user_id', 'guide_type', 'base_price', 'payment_mode',
        'currency', 'duration_minutes', 'min_participants', 'max_participants',
        'min_age', 'difficulty_level', 'languages', 'meeting_point',
        'included_items', 'excluded_items', 'faq', 'cover_image', 'gallery',
        'video_url', 'is_featured', 'is_instant_booking', 'available_spots',
        'status', 'published_at', 'views_count', 'sort_order',
        'promotional_price', 'promotion_starts_at', 'promotion_ends_at',
        'promo_description', 'discount_percentage', 'average_rating',
    ];

    protected $casts = [
        'included_items'      => 'array',
        'excluded_items'      => 'array',
        'faq'                 => 'array',
        'gallery'             => 'array',
        'languages'           => 'array',
        'is_featured'         => 'boolean',
        'is_instant_booking'  => 'boolean',
        'base_price'          => 'decimal:2',
        'promotional_price'   => 'decimal:2',
        'average_rating'      => 'decimal:1',
        'published_at'        => 'datetime',
        'promotion_starts_at' => 'datetime',
        'promotion_ends_at'   => 'datetime',
    ];

    // ════════════════════════════════════════════════════════
    // BOOT — slug unique avec suffix numérique
    // ════════════════════════════════════════════════════════

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $offer) {
            if (empty($offer->slug)) {
                $base = Str::slug($offer->title);
                $slug = $base;
                $i    = 1;

                // Vérifier aussi les offres soft-deleted pour éviter les collisions
                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $offer->slug = $slug;
            }
        });
    }

    // ════════════════════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════════════════════

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function city(): BelongsTo    { return $this->belongsTo(City::class); }
    public function reviews(): HasMany   { return $this->hasMany(Review::class); }
    public function bookings(): HasMany  { return $this->hasMany(Booking::class); }

    public function tiers(): HasMany
    {
        return $this->hasMany(OfferTier::class)->orderBy('sort_order');
    }

    public function activeTiers(): HasMany
    {
        return $this->hasMany(OfferTier::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS — PROMO
    // ════════════════════════════════════════════════════════

    public function getIsPromoAttribute(): bool
    {
        if (empty($this->promotional_price) || (float) $this->promotional_price <= 0) return false;
        if ($this->promotion_starts_at && Carbon::now()->lt($this->promotion_starts_at)) return false;
        if ($this->promotion_ends_at   && Carbon::now()->gt($this->promotion_ends_at))  return false;
        return (float) $this->promotional_price < (float) $this->base_price;
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->is_promo ? (float) $this->promotional_price : (float) $this->base_price;
    }

    public function getPromoDiscountAttribute(): int
    {
        if (! $this->is_promo || (float) $this->base_price <= 0) return 0;
        return (int) round((1 - (float) $this->promotional_price / (float) $this->base_price) * 100);
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS — TIERS
    // ════════════════════════════════════════════════════════

    public function getStartingPriceAttribute(): float
    {
        return $this->activeTiers->min('price') ?? (float) $this->base_price;
    }

    public function getHasTiersAttribute(): bool
    {
        return $this->activeTiers->isNotEmpty();
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS — GUIDE
    // ════════════════════════════════════════════════════════

    public function getGuideTypeLabelAttribute(): string
    {
        return match ($this->guide_type ?? 'agency') {
            'assigned' => 'Guide prédéfini',
            'agency'   => 'Guide agence',
            'on_site'  => 'Guide local du site',
            default    => 'Guide',
        };
    }

    public function getGuideTypeDescriptionAttribute(): string
    {
        return match ($this->guide_type ?? 'agency') {
            'assigned' => 'Votre guide est déjà sélectionné pour cette expérience.',
            'agency'   => 'Un guide certifié DiscovTrip vous sera assigné après confirmation.',
            'on_site'  => 'Ce site dispose de son propre guide officiel accrédité.',
            default    => '',
        };
    }

    public function getShowGuideProfileAttribute(): bool
    {
        return ($this->guide_type ?? 'agency') === 'assigned' && $this->user_id !== null;
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS — VIDÉO
    // ════════════════════════════════════════════════════════

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) return null;

        // YouTube (watch, shorts, embed, youtu.be)
        if (preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $this->video_url, $m
        )) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1] . '?title=0&byline=0&portrait=0';
        }

        return null;
    }

    public function getHasVideoAttribute(): bool
    {
        return $this->video_embed_url !== null;
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS — DIVERS
    // ════════════════════════════════════════════════════════

    public function getDurationFormattedAttribute(): string
    {
        $h   = (int) floor($this->duration_minutes / 60);
        $min = $this->duration_minutes % 60;
        return $h . 'h' . ($min > 0 ? $min . 'min' : '');
    }

    public function getPriceEurAttribute(): int
    {
        return (int) round($this->effective_price / config('discovtrip.eur_rate', 655.957));
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'cultural'   => 'Culturel',
            'gastronomy' => 'Gastronomie',
            'nature'     => 'Nature',
            'adventure'  => 'Aventure',
            'wellness'   => 'Bien-être',
            'urban'      => 'Urbain',
            default      => ucfirst($this->category ?? ''),
        };
    }

    public function getCategoryEmojiAttribute(): string
    {
        return match ($this->category) {
            'cultural'   => '🕌',
            'gastronomy' => '🍽️',
            'nature'     => '🌊',
            'adventure'  => '🏔️',
            'wellness'   => '🧘',
            'urban'      => '🏙️',
            default      => '✨',
        };
    }

    // ════════════════════════════════════════════════════════
    // SCOPES
    // ════════════════════════════════════════════════════════

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNull('deleted_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Offres actuellement en promotion (dates valides).
     */
    public function scopeOnPromo($query)
    {
        return $query
            ->whereNotNull('promotional_price')
            ->where('promotional_price', '>', 0)
            ->whereRaw('promotional_price < base_price')
            ->where(fn ($q) => $q->whereNull('promotion_starts_at')
                                 ->orWhere('promotion_starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('promotion_ends_at')
                                 ->orWhere('promotion_ends_at', '>=', now()));
    }

    /**
     * Offres dont la promotion est expirée (utilisé dans OfferResource Filament).
     */
    public function scopePromoExpired($query)
    {
        return $query
            ->whereNotNull('promotional_price')
            ->whereNotNull('promotion_ends_at')
            ->where('promotion_ends_at', '<', now());
    }
}