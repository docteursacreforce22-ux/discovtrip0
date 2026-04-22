<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => ['required', 'email', 'max:255'],
            // Numéro Bénin 10 chiffres : +229 01 XX XX XX XX
            // Accepte avec/sans +229, avec/sans espaces/tirets
            'phone'    => [
                'nullable',
                'string',
                'regex:/^(\+?229[\s.\-]?)?[0-9]{2}[\s.\-]?[0-9]{2}[\s.\-]?[0-9]{2}[\s.\-]?[0-9]{2}[\s.\-]?[0-9]{2}$/',
            ],
            'subject'  => ['required', 'string', 'in:reservation,information,partenariat,guide,autre'],
            'message'  => ['required', 'string', 'min:20', 'max:2000'],
            'consent'  => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Votre prénom est requis.',
            'last_name.required'  => 'Votre nom est requis.',
            'email.required'      => 'Votre adresse email est requise.',
            'email.email'         => 'Adresse email invalide.',
            'phone.regex'         => 'Numéro invalide. Format attendu : +229 01 XX XX XX XX (10 chiffres).',
            'subject.required'    => 'Veuillez choisir un sujet.',
            'subject.in'          => 'Sujet invalide.',
            'message.required'    => 'Votre message est requis.',
            'message.min'         => 'Votre message doit faire au moins 20 caractères.',
            'consent.accepted'    => 'Vous devez accepter la politique de confidentialité.',
        ];
    }
}