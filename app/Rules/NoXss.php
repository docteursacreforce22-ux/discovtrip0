<?php
declare(strict_types=1);
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoXss implements Rule {
    private const XSS_PATTERNS = [
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<embed\b[^>]*>/i',
        '/<object\b[^>]*>/i',
    ];

    public function passes($attribute, $value): bool {
        if (!is_string($value)) {
            return true;
        }

        foreach (self::XSS_PATTERNS as $pattern) {
            if (preg_match($pattern, $value)) {
                \Log::warning('XSS attempt detected', [
                    'field' => $attribute,
                    'value' => substr($value, 0, 100),
                    'ip' => request()->ip(),
                ]);
                return false;
            }
        }

        return true;
    }

    public function message(): string {
        return 'La valeur fournie contient du contenu non autorisé.';
    }
}
