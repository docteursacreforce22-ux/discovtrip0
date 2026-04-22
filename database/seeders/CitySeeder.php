<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder {
    public function run(): void {
        $this->command->info('🏙️ Seeding cities...');
        
        $cities = [
            ['name' => 'Cotonou', 'country' => 'Bénin', 'latitude' => 6.3703, 'longitude' => 2.3912],
            ['name' => 'Porto-Novo', 'country' => 'Bénin', 'latitude' => 6.4969, 'longitude' => 2.6289],
            ['name' => 'Ouidah', 'country' => 'Bénin', 'latitude' => 6.3625, 'longitude' => 2.0850],
            ['name' => 'Abomey', 'country' => 'Bénin', 'latitude' => 7.1827, 'longitude' => 1.9912],
            ['name' => 'Parakou', 'country' => 'Bénin', 'latitude' => 9.3372, 'longitude' => 2.6103],
            ['name' => 'Ganvié', 'country' => 'Bénin', 'latitude' => 6.4461, 'longitude' => 2.4036],
        ];
        
        foreach ($cities as $city) {
            DB::table('cities')->insert(array_merge($city, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        
        $this->command->info('✅ Cities seeded: ' . count($cities));
    }
}
