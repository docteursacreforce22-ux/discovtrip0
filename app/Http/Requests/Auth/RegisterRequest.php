<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:60'],
            'last_name'  => ['required', 'string', 'min:2', 'max:60'],
            'email'      => ['required', 'email:rfc', 'max:254', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'regex:/^\+?[0-9\s]{8,20}$/'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'consent'    => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.min'      => 'Le prénom doit comporter au moins 2 caractères.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'last_name.min'       => 'Le nom doit comporter au moins 2 caractères.',
            'email.required'      => 'L\'adresse email est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',
            'phone.regex'         => 'Le numéro de téléphone n\'est pas valide (ex: +229 01 00 00 00 00).',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'  => 'La confirmation du mot de passe ne correspond pas.',
            'consent.accepted'    => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }
}