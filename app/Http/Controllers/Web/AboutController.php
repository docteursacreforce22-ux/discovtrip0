<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Offer;
use App\Models\PageContent;
use App\Models\TeamMember;

class AboutController extends Controller
{
    public function show()
    {
        // Stats dynamiques depuis la DB
        $stats = [
            'cities'    => City::where('is_active', true)->count(),
            'offers'    => Offer::where('status', 'published')->count(),
            'travelers' => '500+',
            'rating'    => '4.9',
        ];

        // Contenu hero éditables depuis l'admin (avec fallback)
        $hero = PageContent::get('about', 'hero', [
            'title'    => 'Faire découvrir le Bénin comme il mérite de l\'être',
            'subtitle' => 'DiscovTrip connecte les voyageurs du monde entier aux expériences authentiques du Bénin, portées par des guides locaux certifiés.',
        ]);

        // Mission
        $mission = PageContent::get('about', 'mission', [
            'title' => 'Connecter les voyageurs aux vraies richesses du Bénin',
            'text1' => 'Nous croyons que le tourisme peut être une force positive — pour les voyageurs qui découvrent des cultures extraordinaires, et pour les communautés locales qui partagent leurs savoir-faire et traditions.',
            'text2' => 'Chaque expérience sur DiscovTrip est soigneusement sélectionnée et conçue avec des guides locaux certifiés, garantissant authenticité, qualité et impact positif pour les communautés béninoises.',
        ]);

        // Valeurs
        $values = PageContent::get('about', 'values', [
            ['icon' => 'heart',        'title' => 'Authenticité',   'desc' => 'Des expériences vraies, loin des clichés touristiques'],
            ['icon' => 'shield-alt',   'title' => 'Confiance',      'desc' => 'Guides vérifiés, paiements sécurisés, annulation flexible'],
            ['icon' => 'leaf',         'title' => 'Responsabilité', 'desc' => 'Un tourisme respectueux des communautés et de la nature'],
        ]);

        // Timeline de l'histoire (statique — pas besoin d'admin pour ça)
        $timeline = [
            ['year' => '2022', 'title' => 'L\'idée naît',          'text' => 'Une équipe de passionnés du Bénin décide de créer la première plateforme d\'expériences authentiques du pays.'],
            ['year' => '2023', 'title' => 'Lancement officiel',    'text' => 'DiscovTrip ouvre ses portes avec 20 expériences et 8 guides certifiés sur 4 destinations.'],
            ['year' => '2024', 'title' => 'Croissance rapide',     'text' => 'Plus de 500 voyageurs satisfaits, 50+ expériences et 12 villes couvertes dans tout le Bénin.'],
            ['year' => '2025', 'title' => 'Cap sur l\'Afrique',    'text' => 'Extension vers les destinations frontalières et lancement du programme de guides certifiés DiscovTrip.'],
        ];

        // Équipe depuis la DB
        $team = TeamMember::active()->get();

        // Badges de confiance
        $trustBadges = [
            ['icon' => 'shield-alt',  'title' => 'Guides certifiés',     'sub' => '100% des partenaires vérifiés'],
            ['icon' => 'lock',         'title' => 'Paiement sécurisé',   'sub' => 'Chiffrement SSL 256 bits'],
            ['icon' => 'undo',         'title' => 'Annulation gratuite', 'sub' => 'Remboursement jusqu\'à 48h avant'],
            ['icon' => 'star',         'title' => 'Satisfaction 4.9/5',  'sub' => 'Plus de 500 avis vérifiés'],
        ];

        return view('pages.about', compact(
            'stats',
            'hero',
            'mission',
            'values',
            'timeline',
            'team',
            'trustBadges',
        ));
    }
}