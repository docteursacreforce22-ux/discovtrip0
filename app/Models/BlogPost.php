<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'cover_image',
        'category', 'tags', 'author_id', 'status', 'published_at',
        'reading_time', 'views_count', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'tags'         => 'array',
        'published_at' => 'datetime',
        'views_count'  => 'integer',
        'reading_time' => 'integer',
    ];

    // ── Boot — slug auto ─────────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $post) {
            if (empty($post->slug)) {
                $base = Str::slug($post->title);
                $slug = $base;
                $i    = 1;
                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $post->slug = $slug;
            }

            // Calculer reading_time depuis le contenu (200 mots/min)
            if (empty($post->reading_time) && $post->content) {
                $wordCount          = str_word_count(strip_tags($post->content));
                $post->reading_time = max(1, (int) ceil($wordCount / 200));
            }

            // excerpt auto depuis content si absent
            if (empty($post->excerpt) && $post->content) {
                $post->excerpt = Str::limit(strip_tags($post->content), 280);
            }
        });
    }

    // ── Relations ────────────────────────────────────────────
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Accessors ────────────────────────────────────────────
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'conseils'     => 'Conseils voyage',
            'destinations' => 'Destinations',
            'culture'      => 'Culture & Traditions',
            'pratique'     => 'Guide pratique',
            default        => ucfirst($this->category),
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'conseils'     => '#B8751A',
            'destinations' => '#2A7A4B',
            'culture'      => '#7A3B8C',
            'pratique'     => '#1A5A8C',
            default        => '#666',
        };
    }

    public function getReadingTimeFormattedAttribute(): string
    {
        return $this->reading_time . ' min de lecture';
    }
}