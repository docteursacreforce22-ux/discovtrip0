<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page');     // 'about', 'contact'
            $table->string('section'); // 'mission', 'values', 'contact_info', etc.
            $table->json('content');   // Contenu structuré
            $table->timestamps();

            $table->unique(['page', 'section']);
        });

        // Seed initial — about
        DB::table('page_contents')->insert([
            [
                'page'    => 'about',
                'section' => 'hero',
                'content' => json_encode([
                    'title'    => 'Rendre le Bénin inoubliable',
                    'subtitle' => 'DiscovTrip est né d\'une conviction simple : le Bénin regorge d\'expériences extraordinaires que le monde ne connaît pas encore. Nous sommes là pour changer ça.',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page'    => 'about',
                'section' => 'mission',
                'content' => json_encode([
                    'title' => 'Connecter les curieux aux gardiens du savoir',
                    'text1' => 'Chaque expérience sur DiscovTrip est proposée par un guide local certifié — quelqu\'un qui connaît sa ville par cœur, qui en parle avec fierté, et qui a envie de la partager.',
                    'text2' => 'Notre rôle ? Créer la connexion. Garantir la qualité. Et vous laisser vivre le reste.',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page'    => 'about',
                'section' => 'values',
                'content' => json_encode([
                    ['icon' => 'heart',        'title' => 'Authenticité',    'desc' => 'Nous ne vendons pas des tours packagés. Chaque expérience est vécue avec de vraies personnes, dans de vrais endroits, loin des sentiers battus.'],
                    ['icon' => 'shield-alt',   'title' => 'Confiance',       'desc' => 'Guides vérifiés, paiements sécurisés, annulation flexible. Vous voyagez l\'esprit libre, nous gérons le reste.'],
                    ['icon' => 'leaf',         'title' => 'Responsabilité',  'desc' => '70% des revenus de chaque expérience vont directement au guide local. Voyager avec DiscovTrip, c\'est investir dans les communautés.'],
                    ['icon' => 'star',         'title' => 'Excellence',      'desc' => 'Chaque guide est formé, chaque offre est testée, chaque avis est vérifié. La qualité n\'est pas une option.'],
                    ['icon' => 'globe-africa', 'title' => 'Ouverture',       'desc' => 'Le Bénin mérite d\'être connu. Nous travaillons chaque jour pour faire rayonner cette culture au-delà de ses frontières.'],
                    ['icon' => 'users',        'title' => 'Communauté',      'desc' => 'Guides, voyageurs, partenaires locaux — DiscovTrip est un écosystème bâti sur la réciprocité et le respect mutuel.'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // contact
            [
                'page'    => 'contact',
                'section' => 'info',
                'content' => json_encode([
                    'address'  => 'Cotonou, Bénin — Quartier Cadjèhoun',
                    'email'    => 'contact@discovtrip.com',
                    'phone'    => '+229 01 23 45 67 89',
                    'hours'    => 'Lun–Ven : 8h–18h · Sam : 9h–14h',
                    'response' => 'Réponse garantie sous 24h, 7j/7',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};