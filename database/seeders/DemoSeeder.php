<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Country, City, Offer};
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Créer Bénin
        $benin = Country::create([
            'name' => 'Bénin',
            'slug' => 'benin',
            'code' => 'BJ'
        ]);

        // Créer Cotonou
        $cotonou = City::create([
            'name' => 'Cotonou',
            'slug' => 'cotonou',
            'country_id' => $benin->id
        ]);

        // Créer Porto-Novo
        $portoNovo = City::create([
            'name' => 'Porto-Novo',
            'slug' => 'porto-novo',
            'country_id' => $benin->id
        ]);

        // Offres Cotonou
        Offer::create([
            'title' => 'Visite de Ganvié',
            'slug' => 'visite-ganvie',
            'description' => 'Découvrez le village lacustre de Ganvié, la Venise de l\'Afrique. Une expérience unique sur l\'eau avec les communautés locales.',
            'price' => 15000,
            'status' => 'active',
            'is_featured' => true,
            'city_id' => $cotonou->id,
            'category' => 'culture',
            'duration' => '4h',
            'max_participants' => 8,
            'highlights' => "Balade en pirogue traditionnelle\nRencontre avec les habitants\nDécouverte du marché flottant\nPhotos spectaculaires",
            'included' => "Transport aller-retour\nGuide local francophone\nEau minérale\nGilet de sauvetage",
        ]);

        Offer::create([
            'title' => 'Plage de Fidjrossè',
            'slug' => 'plage-fidjrosse',
            'description' => 'Journée détente sur la plus belle plage de Cotonou. Sable fin, cocotiers et océan Atlantique vous attendent.',
            'price' => 8000,
            'status' => 'active',
            'is_featured' => true,
            'city_id' => $cotonou->id,
            'category' => 'detente',
            'duration' => '6h',
            'max_participants' => 15,
            'highlights' => "Accès plage privée\nTransats et parasols\nActivités nautiques\nBar de plage",
            'included' => "Transat et parasol\nEau minérale\nAccès douches",
        ]);

        Offer::create([
            'title' => 'Atelier Cuisine Béninoise',
            'slug' => 'cuisine-beninoise',
            'description' => 'Apprenez à cuisiner les plats traditionnels béninois avec un chef local. Une immersion culinaire authentique.',
            'price' => 12000,
            'status' => 'active',
            'is_featured' => true,
            'city_id' => $cotonou->id,
            'category' => 'gastronomie',
            'duration' => '3h',
            'max_participants' => 10,
            'highlights' => "Cours avec chef professionnel\nMarché local inclus\nDégustation des plats\nLivret de recettes offert",
            'included' => "Tous les ingrédients\nÉquipement de cuisine\nDégustation\nLivret recettes PDF",
        ]);

        Offer::create([
            'title' => 'Marché Dantokpa',
            'slug' => 'marche-dantokpa',
            'description' => 'Visite guidée du plus grand marché d\'Afrique de l\'Ouest. Une explosion de couleurs, saveurs et cultures.',
            'price' => 10000,
            'status' => 'active',
            'is_featured' => false,
            'city_id' => $cotonou->id,
            'category' => 'culture',
            'duration' => '3h',
            'max_participants' => 6,
        ]);

        Offer::create([
            'title' => 'Musée Fondation Zinsou',
            'slug' => 'musee-zinsou',
            'description' => 'Découvrez l\'art contemporain africain au musée Fondation Zinsou.',
            'price' => 5000,
            'status' => 'active',
            'is_featured' => false,
            'city_id' => $cotonou->id,
            'category' => 'art',
            'duration' => '2h',
            'max_participants' => 20,
        ]);

        Offer::create([
            'title' => 'Palais Royal de Porto-Novo',
            'slug' => 'palais-royal-porto-novo',
            'description' => 'Visite du palais royal et découverte de l\'histoire du royaume de Porto-Novo.',
            'price' => 7000,
            'status' => 'active',
            'is_featured' => true,
            'city_id' => $portoNovo->id,
            'category' => 'culture',
            'duration' => '2h30',
            'max_participants' => 12,
        ]);
    }
}