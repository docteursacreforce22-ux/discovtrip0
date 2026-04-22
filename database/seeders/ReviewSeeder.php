<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Review, Booking};

class ReviewSeeder extends Seeder {
    public function run(): void {
        $this->command->info('⭐ Seeding reviews...');
        
        $completedBookings = Booking::where('status', 'completed')->limit(50)->get();
        
        $comments = [
            'Excellente expérience ! Je recommande vivement.',
            'Guide très professionnel et sympathique.',
            'Superbe découverte, très enrichissant.',
            'Un moment inoubliable, merci !',
            'Bonne expérience dans l\'ensemble.',
            'Très intéressant et bien organisé.',
            'Guide passionné, visite captivante.',
            'Belle activité, à refaire !',
        ];
        
        foreach ($completedBookings as $booking) {
            if (rand(0, 100) > 30) { // 70% des bookings ont une review
                Review::create([
                    'offer_id' => $booking->offer_id,
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'rating' => rand(3, 5),
                    'comment' => $comments[array_rand($comments)],
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
        
        $this->command->info('✅ Reviews seeded: ' . Review::count());
    }
}
