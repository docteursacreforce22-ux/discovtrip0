<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👥 Seeding users...');

        // 1. Admin Account
        User::create([
            'first_name'        => 'Admin',
            'last_name'         => 'Discovtrip',
            'email'             => 'admin@discovtrip.com',
            'password'          => Hash::make('password123!'),
            'phone'             => '+22961000001',
            'role'              => 'admin',
            'email_verified'    => true,
            'email_verified_at' => now(),
            'is_active'         => true,
            'locale'            => 'fr',
            'timezone'          => 'Africa/Porto-Novo',
            'currency'          => 'XOF',
        ]);

        // 2. Partner Accounts (10)
        $partners = [
            ['name' => 'Marie Kouassi',        'city' => 'Cotonou'],
            ['name' => 'Jean-Baptiste Dossou', 'city' => 'Porto-Novo'],
            ['name' => 'Fatoumata Traoré',     'city' => 'Ouidah'],
            ['name' => 'Ibrahim Sawadogo',     'city' => 'Abomey'],
            ['name' => 'Aminata Diallo',       'city' => 'Cotonou'],
            ['name' => 'Yves Akakpo',          'city' => 'Parakou'],
            ['name' => 'Rachelle Hounnou',     'city' => 'Cotonou'],
            ['name' => 'Michel Gbaguidi',      'city' => 'Ouidah'],
            ['name' => 'Sylvie Koffi',         'city' => 'Porto-Novo'],
            ['name' => 'David Assogba',        'city' => 'Cotonou'],
        ];

        foreach ($partners as $index => $partner) {
            $parts     = explode(' ', $partner['name']);
            $firstName = $parts[0];
            $lastName  = $parts[1] ?? '';

            User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => strtolower($firstName) . '@discovtrip.com',
                'password'          => Hash::make('password123!'),
                'phone'             => '+2296100000' . (10 + $index),
                'role'              => 'partner',
                'email_verified'    => true,
                'email_verified_at' => now()->subDays(rand(30, 365)),
                'is_active'         => true,
                'locale'            => 'fr',
                'timezone'          => 'Africa/Porto-Novo',
                'currency'          => 'XOF',
            ]);
        }

        // 3. Client Accounts (20 — allégé pour éviter les doublons)
        $firstNames = ['Sophie','Thomas','Emma','Lucas','Léa','Hugo','Chloé','Alexandre','Camille','Antoine','Julie','Maxime','Sarah','Pierre','Marie','Nicolas','Laura','Julien','Alice','Mathieu'];
        $lastNames  = ['Martin','Bernard','Dubois','Thomas','Robert','Richard','Petit','Durand','Leroy','Moreau'];

        for ($i = 0; $i < 20; $i++) {
            $firstName = $firstNames[$i % count($firstNames)];
            $lastName  = $lastNames[$i  % count($lastNames)];

            User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => strtolower($firstName . '.' . $lastName . $i) . '@example.com',
                'password'          => Hash::make('password123!'),
                'phone'             => '+3367' . rand(10000000, 99999999),
                'role'              => 'client',
                'email_verified'    => true,
                'email_verified_at' => now()->subDays(rand(1, 365)),
                'is_active'         => true,
                'locale'            => ['fr', 'en'][rand(0, 1)],
                'timezone'          => 'Europe/Paris',
                'currency'          => ['EUR', 'USD'][rand(0, 1)],
            ]);
        }

        // 4. Compte client de test
        User::create([
            'first_name'        => 'Test',
            'last_name'         => 'Client',
            'email'             => 'client@discovtrip.com',
            'password'          => Hash::make('password123!'),
            'phone'             => '+33612345678',
            'role'              => 'client',
            'email_verified'    => true,
            'email_verified_at' => now(),
            'is_active'         => true,
            'locale'            => 'fr',
            'timezone'          => 'Europe/Paris',
            'currency'          => 'EUR',
        ]);

        $this->command->info('✅ Users seeded: ' . User::count());
    }
}