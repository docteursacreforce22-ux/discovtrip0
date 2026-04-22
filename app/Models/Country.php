<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Country extends Model
{
    protected $fillable = [
        'name', 'slug', 'code', 'continent',
        'flag_emoji', 'cover_image', 'capital',
        'currency_code', 'currency_name', 'language',
        'population', 'area',
        'description', 'history', 'culture', 'practical_info',
        'meta_title', 'meta_description',
        'is_active', 'is_featured', 'featured_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($country) {
            if (empty($country->slug)) {
                $country->slug = Str::slug($country->name);
            }
        });
    }

    // ── Relations ────────────────────────────────────────────

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function activeCities()
    {
        return $this->hasMany(City::class)->where('is_active', true);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Accessors ────────────────────────────────────────────

    /**
     * Nombre de villes actives avec au moins une offre publiée.
     * Usage : $country->active_cities_count (via withCount)
     */
    public function getActiveCitiesWithOffersCountAttribute(): int
    {
        return $this->cities()
            ->where('is_active', true)
            ->whereHas('offers', fn($q) => $q->where('status', 'published'))
            ->count();
    }
}
