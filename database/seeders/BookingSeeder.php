<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Booking, User, Offer};

class BookingSeeder extends Seeder {
    public function run(): void {
        $this->command->info('📅 Seeding bookings...');
        
        $clients = User::where('role', 'client')->limit(50)->get();
        $offers = Offer::all();
        $statuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled_by_user'];
        
        foreach ($clients as $client) {
            // Chaque client a 1-3 réservations
            $bookingCount = rand(1, 3);
            
            for ($i = 0; $i < $bookingCount; $i++) {
                $offer = $offers->random();
                $status = $statuses[array_rand($statuses)];
                $bookingDate = now()->addDays(rand(-60, 60));
                
                Booking::create([
                    'reference' => 'BK-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 5)),
                    'user_id' => $client->id,
                    'offer_id' => $offer->id,
                    'booking_date' => $bookingDate,
                    'status' => $status,
                    'adults' => rand(1, 4),
                    'children' => rand(0, 2),
                    'infants' => rand(0, 1),
                    'total_price' => $offer->base_price * rand(1, 4),
                    'currency' => 'XOF',
                    'cancellation_policy' => ['flexible', 'moderate', 'strict'][array_rand(['flexible', 'moderate', 'strict'])],
                    'is_paid' => in_array($status, ['confirmed', 'processing', 'completed']),
                    'paid_at' => in_array($status, ['confirmed', 'processing', 'completed']) ? now()->subDays(rand(1, 30)) : null,
                    'confirmation_sent' => in_array($status, ['confirmed', 'processing', 'completed']),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
            }
        }
        
        $this->command->info('✅ Bookings seeded: ' . Booking::count());
    }
}
