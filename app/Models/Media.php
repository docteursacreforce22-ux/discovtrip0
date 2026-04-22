<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Factories\HasFactory};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model {
    use HasFactory;

    protected $fillable = [
        'filename', 'path', 'type', 'mime_type', 'size_bytes',
        'width', 'height', 'alt', 'caption', 'uploaded_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function scopeImages($query) { return $query->where('type', 'image'); }
    public function scopeVideos($query) { return $query->where('type', 'video'); }
}
