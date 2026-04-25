<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature   = 'admin:create';
    protected $description = 'Créer le compte admin depuis les variables de configuration';

    public function handle(): void
    {
        // CORRECTION : utiliser config() au lieu de env()
        // env() retourne null après php artisan config:cache (comportement normal Laravel)
        $email    = config('discovtrip.admin_email');
        $password = config('discovtrip.admin_password');

        // Fallback : lire directement depuis $_ENV si config:cache pas encore fait
        if (! $email) {
            $email    = $_ENV['ADMIN_EMAIL']    ?? null;
            $password = $_ENV['ADMIN_PASSWORD'] ?? null;
        }

        if (! $email || ! $password) {
            $this->info('ADMIN_EMAIL ou ADMIN_PASSWORD non défini — création admin ignorée.');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->info('Admin déjà existant (' . $email . ') — ignoré.');
            return;
        }

        User::forceCreate([
            'first_name'     => 'Admin',
            'last_name'      => 'DiscovTrip',
            'email'          => $email,
            'password'       => Hash::make($password),
            'role'           => 'admin',
            'is_active'      => true,
            'is_banned'      => false,
            'email_verified' => true,
        ]);

        $this->info('✅ Compte admin créé : ' . $email);
    }
}
