<?php
declare(strict_types=1);
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPhone implements Rule {
    public function passes($attribute, $value): bool {
        // Format E.164: +[country code][number]
        // Ex: +33612345678, +22997123456
        return preg_match('/^\+[1-9]\d{1,14}$/', $value) === 1;
    }

    public function message(): string {
        return 'Le numéro de téléphone doit être au format international (+33612345678).';
    }
}
