<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Offer, User};

class OfferSeeder extends Seeder {
    public function run(): void {
        $this->command->info('🎯 Seeding offers...');
        
        $partners = User::where('role', 'partner')->get();
        $categories = ['tour', 'activity', 'excursion', 'cultural', 'adventure', 'gastronomy', 'nature', 'family'];
        
        $offers = [
            ['title' => 'Visite guidée de Cotonou', 'category' => 'tour', 'price' => 15000, 'duration' => 180, 'city_id' => 1],
            ['title' => 'Découverte du Lac Nokoué', 'category' => 'nature', 'price' => 25000, 'duration' => 240, 'city_id' => 1],
            ['title' => 'Route des Esclaves Ouidah', 'category' => 'cultural', 'price' => 18000, 'duration' => 120, 'city_id' => 3],
            ['title' => 'Palais Royaux d\'Abomey', 'category' => 'cultural', 'price' => 12000, 'duration' => 90, 'city_id' => 4],
            ['title' => 'Cuisine béninoise traditionnelle', 'category' => 'gastronomy', 'price' => 20000, 'duration' => 150, 'city_id' => 1],
            ['title' => 'Marché Dantokpa Explorer', 'category' => 'tour', 'price' => 8000, 'duration' => 120, 'city_id' => 1],
            ['title' => 'Village lacustre de Ganvié', 'category' => 'cultural', 'price' => 22000, 'duration' => 300, 'city_id' => 6],
            ['title' => 'Parc National de la Pendjari', 'category' => 'nature', 'price' => 85000, 'duration' => 480, 'city_id' => 5],
            ['title' => 'Cours de percussion africaine', 'category' => 'activity', 'price' => 15000, 'duration' => 120, 'city_id' => 1],
            ['title' => 'Safari photo Pendjari', 'category' => 'adventure', 'price' => 95000, 'duration' => 600, 'city_id' => 5],
        ];
        
        foreach ($offers as $index => $offerData) {
            Offer::create([
                'title' => $offerData['title'],
                'description' => 'Une expérience unique pour découvrir ' . $offerData['title'] . '. Profitez d\'une visite exceptionnelle avec un guide local expérimenté.',
                'short_description' => 'Découvrez ' . $offerData['title'],
                'slug' => \Illuminate\Support\Str::slug($offerData['title']),
                'category' => $offerData['category'],
                'status' => 'published',
                'city_id' => $offerData['city_id'],
                'base_price' => $offerData['price'],
                'currency' => 'XOF',
                'duration_minutes' => $offerData['duration'],
                'min_participants' => rand(1, 2),
                'max_participants' => rand(10, 20),
                'is_featured' => $index < 3,
                'average_rating' => rand(40, 50) / 10,
                'reviews_count' => rand(5, 50),
                'bookings_count' => rand(10, 100),
                'published_at' => now()->subDays(rand(30, 365)),
                'languages' => json_encode(['fr', 'en']),
            ]);
        }
        
        $this->command->info('✅ Offers seeded: ' . Offer::count());
    }
}
