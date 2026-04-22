<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * INPUT SANITIZATION MIDDLEWARE
 *
 * Rôle unique : détecter et SUPPRIMER le contenu XSS/injection (scripts, iframes…).
 * Ce middleware NE encode PAS les données — Blade le fait déjà via {{ }}.
 * Encoder ici causerait un double encodage : "L'Afrique" → "L&amp;#039;Afrique".
 */
class SanitizeInput
{
    /**
     * Champs exemptés (mots de passe, tokens — ne jamais toucher)
     */
    private const EXEMPT_FIELDS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'signature',
        '_token',
    ];

    /**
     * Patterns XSS à détecter et supprimer
     */
    private const XSS_PATTERNS = [
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
        '/javascript:/i',
        '/on\w+\s*=/i',   // onclick=, onload=, etc.
        '/<embed\b[^>]*>/i',
        '/<object\b[^>]*>/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $this->sanitizeRequest($request);
        return $next($request);
    }

    private function sanitizeRequest(Request $request): void
    {
        if ($request->query->count() > 0) {
            $request->query->replace($this->sanitizeArray($request->query->all()));
        }

        if ($request->request->count() > 0) {
            $request->request->replace($this->sanitizeArray($request->request->all()));
        }

        if ($request->isJson() && $request->getContent()) {
            $json = json_decode($request->getContent(), true);
            if (is_array($json)) {
                $request->merge($this->sanitizeArray($json));
            }
        }
    }

    private function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (in_array($key, self::EXEMPT_FIELDS, true)) {
                $sanitized[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value, $key);
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function sanitizeString(string $value, string $key): string
    {
        // Supprimer les null bytes
        $value = str_replace(chr(0), '', $value);

        // Détecter et supprimer le contenu XSS
        if ($this->containsXss($value)) {
            \Log::warning('XSS attempt detected', [
                'field' => $key,
                'value' => substr($value, 0, 100),
                'ip'    => request()->ip(),
            ]);
            $value = $this->removeXss($value);
        }

        // IMPORTANT : on retourne la valeur BRUTE, sans htmlspecialchars.
        // Blade encode automatiquement via {{ $var }}.
        // Double-encoder ici casserait les apostrophes françaises et les guillemets.
        return $value;
    }

    private function containsXss(string $value): bool
    {
        foreach (self::XSS_PATTERNS as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }

    private function removeXss(string $value): string
    {
        foreach (self::XSS_PATTERNS as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }
        return $value;
    }

    /**
     * Sanitize un nom de fichier uploadé (utilisable statiquement dans les controllers)
     */
    public static function sanitizeFilename(string $filename): string
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        if (strlen($filename) > 255) {
            $ext      = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($basename, 0, 250) . '.' . $ext;
        }

        return $filename;
    }
}