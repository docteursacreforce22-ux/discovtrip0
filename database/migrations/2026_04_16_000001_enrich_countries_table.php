<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enrichit la table countries pour la page détail pays.
 * Tous les champs sont nullable — aucun pays existant n'est cassé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Visuel
            $table->string('flag_emoji', 10)->nullable()->after('code');      // 🇧🇯
            $table->string('cover_image')->nullable()->after('flag_emoji');   // image de couverture
            $table->string('capital')->nullable()->after('cover_image');      // Cotonou
            $table->string('currency_code', 10)->nullable()->after('capital'); // XOF
            $table->string('currency_name')->nullable()->after('currency_code'); // Franc CFA
            $table->string('language')->nullable()->after('currency_name');   // Français
            $table->string('population')->nullable();                          // "13 millions"
            $table->string('area')->nullable();                                // "114 763 km²"

            // Contenu éditorial
            $table->text('description')->nullable();
            $table->text('history')->nullable();
            $table->text('culture')->nullable();
            $table->text('practical_info')->nullable();   // visa, santé, sécurité...

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            // Activation
            $table->boolean('is_active')->default(true)->after('continent');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->integer('featured_order')->default(0)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'flag_emoji', 'cover_image', 'capital',
                'currency_code', 'currency_name', 'language',
                'population', 'area',
                'description', 'history', 'culture', 'practical_info',
                'meta_title', 'meta_description',
                'is_active', 'is_featured', 'featured_order',
            ]);
        });
    }
};
