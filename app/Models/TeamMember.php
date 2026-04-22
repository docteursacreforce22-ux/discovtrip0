<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo',
        'linkedin_url',
        'email',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    // Initiales pour l'avatar placeholder
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(
            (substr($parts[0], 0, 1)) .
            (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
        );
    }
}