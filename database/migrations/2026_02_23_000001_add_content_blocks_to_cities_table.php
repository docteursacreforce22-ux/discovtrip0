<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Bloc "Pourquoi y aller" — 3 raisons avec icône + titre + description
            $table->json('highlights')->nullable()->after('description')
                  ->comment('[{icon, title, description}]');

            // Bloc "Les incontournables" — lieux clés à visiter
            $table->json('landmarks')->nullable()->after('highlights')
                  ->comment('[{name, description, emoji}]');

            // Bloc "Infos pratiques"
            $table->string('how_to_get_there')->nullable()->after('landmarks')
                  ->comment('Comment rejoindre la ville depuis Cotonou');
            $table->string('best_time_detail')->nullable()->after('how_to_get_there')
                  ->comment('Détail sur la période idéale');
            $table->string('budget_range')->nullable()->after('best_time_detail')
                  ->comment('Ex: 30 000 – 80 000 FCFA / jour');

            // Bloc "Le saviez-vous" — faits culturels marquants
            $table->json('fun_facts')->nullable()->after('budget_range')
                  ->comment('[{fact}]');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'highlights',
                'landmarks',
                'how_to_get_there',
                'best_time_detail',
                'budget_range',
                'fun_facts',
            ]);
        });
    }
};