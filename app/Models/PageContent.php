<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageContent extends Model
{
    protected $fillable = ['page', 'section', 'content'];

    protected $casts = ['content' => 'array'];

    // Récupère une section avec cache 10 min
    public static function get(string $page, string $section, array $default = []): array
    {
        return Cache::remember(
            "page_content_{$page}_{$section}",
            600,
            fn() => static::where('page', $page)
                          ->where('section', $section)
                          ->first()
                          ?->content ?? $default
        );
    }

    // Invalide le cache quand on sauvegarde
    protected static function booted(): void
    {
        static::saved(function (self $model) {
            Cache::forget("page_content_{$model->page}_{$model->section}");
        });
    }
}