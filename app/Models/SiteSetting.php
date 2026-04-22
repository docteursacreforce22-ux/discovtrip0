<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * Récupérer une valeur de setting
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Si c'est une image, retourner le chemin complet
        if ($setting->type === 'image' && $setting->value) {
            return Storage::url($setting->value);
        }

        // Si c'est du JSON, décoder
        if ($setting->type === 'json' && $setting->value) {
            return json_decode($setting->value, true);
        }

        return $setting->value;
    }

    /**
     * Définir une valeur de setting
     */
    public static function set(string $key, $value, string $type = 'text', string $description = null)
    {
        // Si c'est un tableau, encoder en JSON
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description
            ]
        );
    }

    /**
     * Upload une image pour un setting
     */
    public static function uploadImage(string $key, $file, string $description = null)
    {
        $path = $file->store('settings', 'public');
        
        return static::set($key, $path, 'image', $description);
    }
}