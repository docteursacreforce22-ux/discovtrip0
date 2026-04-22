<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use App\Models\SiteSetting;

class TestimonialsAndSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Settings par défaut
        SiteSetting::set('site_name', 'DiscovTrip', 'text', 'Nom du site');
        SiteSetting::set('site_tagline', 'Découvrez le Bénin Autrement', 'text', 'Slogan du site');
        SiteSetting::set('contact_email', 'contact@discovtrip.com', 'text', 'Email de contact');
        SiteSetting::set('contact_phone', '+229 XX XX XX XX', 'text', 'Téléphone');
        
        // Hero image (sera uploadée via admin)
        SiteSetting::set('hero_home_image', null, 'image', 'Image du hero de la page d\'accueil');

        // Témoignages
        $testimonials = [
            [
                'client_name' => 'Sophie Martin',
                'client_title' => 'Voyageuse de Paris',
                'testimonial' => 'Une expérience absolument magique ! Le guide était passionné et nous a fait découvrir des lieux secrets d\'Ouidah. Je recommande les yeux fermés.',
                'rating' => 5,
                'offer_title' => 'Visite d\'Ouidah',
                'travel_date' => now()->subMonths(2),
                'is_featured' => true,
                'is_published' => true,
                'order' => 1
            ],
            [
                'client_name' => 'Marc Dubois',
                'client_title' => 'Entrepreneur, Bruxelles',
                'testimonial' => 'J\'ai été impressionné par le professionnalisme et l\'authenticité de l\'expérience. Ganvié est vraiment unique et notre guide connaissait chaque recoin.',
                'rating' => 5,
                'offer_title' => 'Cité lacustre de Ganvié',
                'travel_date' => now()->subMonths(1),
                'is_featured' => true,
                'is_published' => true,
                'order' => 2
            ],
            [
                'client_name' => 'Aminata Diallo',
                'client_title' => 'Photographe, Dakar',
                'testimonial' => 'Les couleurs, les sourires, l\'accueil chaleureux... DiscovTrip m\'a offert bien plus qu\'une visite touristique. C\'était un vrai voyage culturel.',
                'rating' => 5,
                'offer_title' => 'Marché Dantokpa',
                'travel_date' => now()->subWeeks(3),
                'is_featured' => true,
                'is_published' => true,
                'order' => 3
            ],
            [
                'client_name' => 'Pierre Lefebvre',
                'client_title' => 'Retraité, Lyon',
                'testimonial' => 'À 65 ans, j\'ai découvert le Bénin comme jamais je ne l\'aurais imaginé. Merci pour cette organisation impeccable et ces moments inoubliables.',
                'rating' => 5,
                'offer_title' => 'Palais royaux d\'Abomey',
                'travel_date' => now()->subMonths(4),
                'is_featured' => false,
                'is_published' => true,
                'order' => 4
            ],
            [
                'client_name' => 'Julie Mercier',
                'client_title' => 'Enseignante, Montréal',
                'testimonial' => 'Je cherchais une expérience authentique et c\'est exactement ce que j\'ai eu. Les rencontres avec les artisans locaux resteront gravées dans ma mémoire.',
                'rating' => 5,
                'offer_title' => 'Artisanat à Porto-Novo',
                'travel_date' => now()->subWeeks(5),
                'is_featured' => true,
                'is_published' => true,
                'order' => 5
            ],
            [
                'client_name' => 'Thomas Bernard',
                'client_title' => 'Consultant, Genève',
                'testimonial' => 'Excellent rapport qualité-prix et une équipe vraiment à l\'écoute. J\'ai pu personnaliser mon parcours et tout s\'est déroulé parfaitement.',
                'rating' => 5,
                'offer_title' => 'Circuit personnalisé',
                'travel_date' => now()->subMonths(3),
                'is_featured' => false,
                'is_published' => true,
                'order' => 6
            ]
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::create($testimonialData);
        }

        $this->command->info('✅ ' . count($testimonials) . ' témoignages créés');
        $this->command->info('✅ Settings initialisés');
    }
}