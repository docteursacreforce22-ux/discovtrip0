<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Métadonnées pratiques pour le voyageur
            $table->string('distance_from_cotonou')->nullable()->after('cover_image')
                  ->comment('Ex: 45 min, 2h30, Base');

            $table->string('duration_days')->nullable()->after('distance_from_cotonou')
                  ->comment('Ex: 1 jour, 2–3 jours');

            $table->string('best_season')->nullable()->after('duration_days')
                  ->comment('Ex: Nov–Avr, Toute l\'année');

            $table->string('category')->nullable()->default('cultural')->after('best_season')
                  ->comment('urban, historical, nature, coastal');

            // Note calculée depuis les reviews (mise à jour via observer ou commande)
            $table->decimal('average_rating', 3, 1)->default(0)->after('category');

            // Ordre d'affichage pour les featured
            $table->unsignedSmallInteger('featured_order')->default(99)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'distance_from_cotonou',
                'duration_days',
                'best_season',
                'category',
                'average_rating',
                'featured_order',
            ]);
        });
    }
};