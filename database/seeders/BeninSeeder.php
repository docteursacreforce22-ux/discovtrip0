<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Offer;

class BeninSeeder extends Seeder
{
    /**
     * Seed the application's database with Benin data.
     */
    public function run(): void
    {
        // 1. Créer le pays Bénin
        $benin = Country::create([
            'name' => 'Bénin',
            'slug' => 'benin',
            'code' => 'BJ',
            'continent' => 'Afrique',
        ]);

        // 2. Créer les villes béninoises
        $cities = [
            ['name' => 'Cotonou', 'latitude' => 6.3703, 'longitude' => 2.3912],
            ['name' => 'Porto-Novo', 'latitude' => 6.4969, 'longitude' => 2.6289],
            ['name' => 'Ouidah', 'latitude' => 6.3636, 'longitude' => 2.0852],
            ['name' => 'Ganvié', 'latitude' => 6.4667, 'longitude' => 2.4167],
            ['name' => 'Abomey', 'latitude' => 7.1833, 'longitude' => 1.9833],
            ['name' => 'Parakou', 'latitude' => 9.3403, 'longitude' => 2.6306],
            ['name' => 'Grand-Popo', 'latitude' => 6.2833, 'longitude' => 1.8167],
            ['name' => 'Natitingou', 'latitude' => 10.3167, 'longitude' => 1.3667],
            ['name' => 'Djougou', 'latitude' => 9.7, 'longitude' => 1.6667],
            ['name' => 'Bohicon', 'latitude' => 7.1667, 'longitude' => 2.0667],
        ];

        $createdCities = [];
        foreach ($cities as $cityData) {
            $createdCities[] = City::create([
                'name' => $cityData['name'],
                'slug' => \Illuminate\Support\Str::slug($cityData['name']),
                'country_id' => $benin->id,
                'latitude' => $cityData['latitude'],
                'longitude' => $cityData['longitude'],
            ]);
        }

        // 3. Créer des offres variées
        $offers = [
            // COTONOU
            [
                'title' => 'Tour Gastronomique de Cotonou',
                'city' => 'Cotonou',
                'category' => 'gastronomy',
                'description' => 'Découvrez les saveurs authentiques du Bénin à travers un parcours culinaire dans les meilleurs maquis et restaurants de Cotonou.',
                'base_price' => 25000,
                'duration_minutes' => 180,
                'max_participants' => 12,
                'is_featured' => true,
                'included' => ['Guide francophone', 'Dégustation 6 plats', 'Boissons locales', 'Transport'],
                'excluded' => ['Pourboires', 'Achats personnels'],
            ],
            [
                'title' => 'Marché Dantokpa & Artisanat Local',
                'city' => 'Cotonou',
                'category' => 'cultural',
                'description' => 'Explorez le plus grand marché d\'Afrique de l\'Ouest et découvrez l\'artisanat béninois authentique.',
                'base_price' => 15000,
                'duration_minutes' => 120,
                'max_participants' => 8,
                'is_featured' => true,
                'included' => ['Guide expérimenté', 'Visite guidée', 'Dégustation sodabi'],
                'excluded' => ['Achats personnels', 'Transport'],
            ],

            // OUIDAH
            [
                'title' => 'Route des Esclaves & Vaudou',
                'city' => 'Ouidah',
                'category' => 'cultural',
                'description' => 'Un voyage émouvant à travers l\'histoire de la traite négrière et la découverte du vaudou béninois.',
                'base_price' => 35000,
                'duration_minutes' => 240,
                'max_participants' => 15,
                'is_featured' => true,
                'included' => ['Guide historien', 'Entrées musées', 'Cérémonie vaudou', 'Transport A/R Cotonou'],
                'excluded' => ['Repas', 'Pourboires'],
            ],
            [
                'title' => 'Plage de Ouidah & Détente',
                'city' => 'Ouidah',
                'category' => 'nature',
                'description' => 'Journée relaxante sur les plages de sable fin de Ouidah avec déjeuner de fruits de mer.',
                'base_price' => 20000,
                'duration_minutes' => 360,
                'max_participants' => 20,
                'is_featured' => false,
                'included' => ['Transat', 'Parasol', 'Déjeuner fruits de mer'],
                'excluded' => ['Boissons alcoolisées', 'Activités nautiques'],
            ],

            // GANVIÉ
            [
                'title' => 'Ganvié : Venise de l\'Afrique en Pirogue',
                'city' => 'Ganvié',
                'category' => 'cultural',
                'description' => 'Découvrez le plus grand village lacustre d\'Afrique, construit sur pilotis au milieu du lac Nokoué.',
                'base_price' => 18000,
                'duration_minutes' => 150,
                'max_participants' => 10,
                'is_featured' => true,
                'included' => ['Pirogue motorisée', 'Guide Tofinu', 'Visite école flottante', 'Dégustation poisson braisé'],
                'excluded' => ['Achats artisanat', 'Pourboires piroguier'],
            ],

            // ABOMEY
            [
                'title' => 'Palais Royaux d\'Abomey',
                'city' => 'Abomey',
                'category' => 'cultural',
                'description' => 'Visite des palais royaux classés UNESCO, vestiges de l\'ancien royaume du Dahomey.',
                'base_price' => 30000,
                'duration_minutes' => 180,
                'max_participants' => 12,
                'is_featured' => true,
                'included' => ['Guide historien', 'Entrée musée', 'Transport A/R', 'Eau minérale'],
                'excluded' => ['Repas', 'Photos/vidéos'],
            ],

            // PORTO-NOVO
            [
                'title' => 'Porto-Novo : Capitale Culturelle',
                'city' => 'Porto-Novo',
                'category' => 'cultural',
                'description' => 'Découverte de la capitale administrative avec ses musées, architecture afro-brésilienne et marchés colorés.',
                'base_price' => 22000,
                'duration_minutes' => 240,
                'max_participants' => 10,
                'is_featured' => false,
                'included' => ['Guide culturel', 'Entrées 3 musées', 'Transport'],
                'excluded' => ['Repas', 'Souvenirs'],
            ],

            // PARAKOU
            [
                'title' => 'Parakou & Chutes de Kota',
                'city' => 'Parakou',
                'category' => 'nature',
                'description' => 'Excursion vers les magnifiques chutes de Kota et découverte de la ville carrefour du Nord.',
                'base_price' => 45000,
                'duration_minutes' => 480,
                'max_participants' => 8,
                'is_featured' => false,
                'included' => ['4x4 tout-terrain', 'Guide nature', 'Pique-nique', 'Baignade aux chutes'],
                'excluded' => ['Boissons alcoolisées', 'Hébergement'],
            ],

            // GRAND-POPO
            [
                'title' => 'Grand-Popo : Plage & Bouche du Roy',
                'city' => 'Grand-Popo',
                'category' => 'nature',
                'description' => 'Journée plage et découverte de l\'embouchure du fleuve Mono dans l\'océan Atlantique.',
                'base_price' => 28000,
                'duration_minutes' => 420,
                'max_participants' => 15,
                'is_featured' => false,
                'included' => ['Transport A/R', 'Déjeuner créole', 'Balade en pirogue', 'Guide local'],
                'excluded' => ['Boissons', 'Activités nautiques'],
            ],

            // NATITINGOU
            [
                'title' => 'Tata Somba & Culture Batammariba',
                'city' => 'Natitingou',
                'category' => 'cultural',
                'description' => 'Découverte des maisons fortifiées Tata Somba et immersion dans la culture Batammariba.',
                'base_price' => 50000,
                'duration_minutes' => 600,
                'max_participants' => 6,
                'is_featured' => true,
                'included' => ['4x4', 'Guide Batammariba', '2 repas', 'Visite 3 Tata Somba', 'Dégustation bière de mil'],
                'excluded' => ['Hébergement', 'Achats artisanat'],
            ],
        ];

        foreach ($offers as $offerData) {
            $city = collect($createdCities)->firstWhere('name', $offerData['city']);
            
            if ($city) {
                Offer::create([
                    'title' => $offerData['title'],
                    'slug' => \Illuminate\Support\Str::slug($offerData['title']),
                    'city_id' => $city->id,
                    'category' => $offerData['category'],
                    'description' => $offerData['description'],
                    'long_description' => $offerData['description'] . "\n\nCette expérience vous permettra de découvrir les richesses culturelles et naturelles du Bénin de manière authentique et immersive.",
                    'base_price' => $offerData['base_price'],
                    'duration_minutes' => $offerData['duration_minutes'],
                    'max_participants' => $offerData['max_participants'],
                    'difficulty_level' => 'easy',
                    'min_age' => 0,
                    'status' => 'published',
                    'is_featured' => $offerData['is_featured'],
                    'is_instant_booking' => true,
                    'available_spots' => rand(5, 15),
                    'included_items' => json_encode($offerData['included']),  // ← CHANGEMENT ICI
                    'excluded_items' => json_encode($offerData['excluded']),  // ← CHANGEMENT ICI
                ]);
            }
        }

        $this->command->info('✅ Bénin seed completed!');
        $this->command->info('✅ 1 Pays créé');
        $this->command->info('✅ ' . count($createdCities) . ' Villes créées');
        $this->command->info('✅ ' . count($offers) . ' Offres créées');
    }
}