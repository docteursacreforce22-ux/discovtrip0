<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Créer le compte admin depuis les variables env';

    public function handle(): void
    {
        $email    = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$email || !$password) {
            $this->info('ADMIN_EMAIL ou ADMIN_PASSWORD non défini — ignoré.');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->info('Admin déjà existant — ignoré.');
            return;
        }

        $user = new User();
        $user->first_name = 'Admin';
        $user->last_name  = 'DiscovTrip';
        $user->email      = $email;
        $user->forceFill([
            'password'       => \Illuminate\Support\Facades\Hash::make($password),
            'role'           => 'admin',
            'is_active'      => true,
            'is_banned'      => false,
            'email_verified' => true,
        ])->save();

        $this->info('✅ Admin créé : ' . $email);
    }
}