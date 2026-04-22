<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Spotlight;

class SpotlightSeeder extends Seeder
{
    public function run(): void
    {
        Spotlight::create([
            'title' => 'Les Guerrières Amazones du Dahomey',
            'subtitle' => 'Symboles de courage et fierté nationale',
            'description' => 'Découvrez l\'histoire fascinante des Mino, ces femmes d\'élite qui ont défendu le royaume du Dahomey aux XVIIIe et XIXe siècles. Leur courage et leur discipline militaire inspirent encore aujourd\'hui le Bénin moderne.',
            'badge_text' => 'Patrimoine Béninois',
            'badge_icon' => 'fa-crown',
            'highlight_word' => 'Amazones du Dahomey',
            'stat1_value' => '1729',
            'stat1_label' => 'Année de création',
            'stat2_value' => '6000+',
            'stat2_label' => 'Guerrières',
            'stat3_value' => '2 siècles',
            'stat3_label' => 'De légende',
            'cta1_label' => 'Découvrir l\'histoire',
            'cta1_url' => '/about',
            'cta2_label' => 'Visiter Abomey',
            'cta2_url' => '/offers?search=Abomey',
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }
}