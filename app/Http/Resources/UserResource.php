<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ($this->last_name ? ' ' . $this->last_name : ''),
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'is_email_verified' => (bool) $this->email_verified,
            'has_2fa' => (bool) $this->two_factor_enabled,
            'profile_picture' => $this->profile_picture,
            'bio' => $this->bio,
            'locale' => $this->locale ?? 'fr',
            'timezone' => $this->timezone ?? 'UTC',
            'currency' => $this->currency,
            'created_at' => $this->created_at?->toISOString(),
            'last_login_at' => $this->last_login_at?->toISOString(),
        ];
    }
}
