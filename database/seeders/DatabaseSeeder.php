<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        $this->call([
            UserSeeder::class,
            // CitySeeder::class, // Décommenter si nécessaire
            OfferSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            ReviewSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed!');
        $this->command->newLine();
        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        $this->command->info('📊 Summary:');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Users',    \App\Models\User::count()],
                ['Cities',   \App\Models\City::count()],
                ['Offers',   \App\Models\Offer::count()],
                ['Bookings', \App\Models\Booking::count()],
                ['Payments', \App\Models\Payment::count()],
                ['Reviews',  \App\Models\Review::count()],
            ]
        );

        $this->command->newLine();
        // NOTE : Les comptes de démo sont définis dans UserSeeder.
        // Ne jamais afficher de mots de passe en clair ici (apparaît dans les logs serveur).
        $this->command->info('Seeding terminé. Consultez UserSeeder pour les comptes de démo.');
    }
}