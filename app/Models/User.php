<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\{SoftDeletes, Factories\HasFactory};
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany};
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'is_banned',
        'ban_reason',
        'email_verified',
        'email_verified_at',
        'two_factor_enabled',
        'locale',
        'timezone',
        'currency',
        'profile_picture',
        'bio',
        'birthday',
        'gender',
        'nationality',
        'preferred_language',
        'travel_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified'            => 'boolean',
        'email_verified_at'         => 'datetime',
        'two_factor_enabled'        => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'active_sessions'           => 'array',
        'is_active'                 => 'boolean',
        'is_banned'                 => 'boolean',
        'birthday'                  => 'date',
        'travel_preferences'        => 'array',
    ];

    protected $appends = ['name', 'full_name'];

    // ════════════════════════════════════════════════════════
    // FILAMENT
    // ════════════════════════════════════════════════════════

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && ! $this->is_banned && $this->is_active;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    // ════════════════════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════════════════════

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Offres créées par ce guide/partenaire.
     * CORRECTION : la FK est user_id (migration 2026_03_05_000001_add_user_id_to_offers)
     * et non partner_id.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'user_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedOffers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'wishlists')->withTimestamps();
    }

    // ════════════════════════════════════════════════════════
    // SCOPES
    // ════════════════════════════════════════════════════════

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_banned', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('email_verified', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // ════════════════════════════════════════════════════════
    // ACCESSORS
    // ════════════════════════════════════════════════════════

    /**
     * Nom complet ou email en fallback — utilisé partout dans les vues et Filament.
     */
    public function getNameAttribute(): string
    {
        $full = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        return $full !== '' ? $full : ($this->email ?? 'Utilisateur');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Initiales pour les avatars (ex: "JD" pour Jean Dupont).
     */
    public function getInitialsAttribute(): string
    {
        $first = strtoupper(substr($this->first_name ?? '', 0, 1));
        $last  = strtoupper(substr($this->last_name  ?? '', 0, 1));
        $initials = $first . $last;
        return $initials !== '' ? $initials : strtoupper(substr($this->email ?? 'U', 0, 1));
    }

    /**
     * URL de l'avatar — photo de profil ou génération automatique.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
             . '&background=B8751A&color=FDFAF6&bold=true&size=128';
    }

    // ════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPartner(): bool
    {
        return $this->role === 'partner';
    }

    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }

    /**
     * Vérifie si l'utilisateur a mis une offre en favoris.
     */
    public function hasWishlisted(int $offerId): bool
    {
        return $this->wishlists()->where('offer_id', $offerId)->exists();
    }
}