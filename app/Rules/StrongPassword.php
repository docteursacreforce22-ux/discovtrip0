<?php
declare(strict_types=1);
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule {
    private array $failures = [];

    public function passes($attribute, $value): bool {
        $this->failures = [];
        
        $minLength = config('security.password.min_length', 12);
        
        if (strlen($value) < $minLength) {
            $this->failures[] = "au moins {$minLength} caractères";
        }
        
        if (config('security.password.require_uppercase', true) && !preg_match('/[A-Z]/', $value)) {
            $this->failures[] = "au moins une majuscule";
        }
        
        if (config('security.password.require_lowercase', true) && !preg_match('/[a-z]/', $value)) {
            $this->failures[] = "au moins une minuscule";
        }
        
        if (config('security.password.require_numbers', true) && !preg_match('/[0-9]/', $value)) {
            $this->failures[] = "au moins un chiffre";
        }
        
        if (config('security.password.require_special', true) && !preg_match('/[^A-Za-z0-9]/', $value)) {
            $this->failures[] = "au moins un caractère spécial";
        }
        
        return empty($this->failures);
    }

    public function message(): string {
        return 'Le mot de passe doit contenir ' . implode(', ', $this->failures) . '.';
    }
}
