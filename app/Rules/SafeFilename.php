<?php
declare(strict_types=1);
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SafeFilename implements Rule {
    public function passes($attribute, $value): bool {
        if (!is_string($value)) {
            return false;
        }

        // Pas de traversal de répertoire
        if (str_contains($value, '..') || str_contains($value, '/') || str_contains($value, '\\')) {
            return false;
        }

        // Seulement caractères alphanumériques, tirets, underscores, points
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $value)) {
            return false;
        }

        // Pas d'extension dangereuse
        $dangerousExtensions = [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'pht', 'phar',
            'exe', 'bat', 'cmd', 'com', 'sh', 'bash',
            'js', 'jsp', 'asp', 'aspx',
        ];

        $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }

        return true;
    }

    public function message(): string {
        return 'Le nom de fichier contient des caractères non autorisés.';
    }
}
