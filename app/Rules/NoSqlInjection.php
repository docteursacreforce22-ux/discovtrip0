<?php
declare(strict_types=1);
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoSqlInjection implements Rule {
    private const SQL_PATTERNS = [
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bSELECT\b.*\bFROM\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bUPDATE\b.*\bSET\b)/i',
        '/(\'|\")(\s)*(OR|AND)(\s)*(\d+|\'|\")/i',
        '/(\bEXEC\b|\bEXECUTE\b)/i',
    ];

    public function passes($attribute, $value): bool {
        if (!is_string($value)) {
            return true;
        }

        foreach (self::SQL_PATTERNS as $pattern) {
            if (preg_match($pattern, $value)) {
                \Log::warning('SQL Injection attempt detected', [
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
        return 'La valeur fournie contient des caractères non autorisés.';
    }
}
