<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter colonnes manquantes à countries
        if (!Schema::hasColumn('countries', 'slug')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->string('slug')->unique()->after('name');
            });
        }
        
        if (!Schema::hasColumn('countries', 'code')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->string('code', 2)->after('slug');
            });
        }
        
        if (!Schema::hasColumn('countries', 'continent')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->string('continent')->nullable()->after('code');
            });
        }

        // Ajouter colonnes manquantes à cities
        if (!Schema::hasColumn('cities', 'slug')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('slug')->unique()->after('name');
            });
        }
        
        if (!Schema::hasColumn('cities', 'latitude')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('country_id');
            });
        }
        
        if (!Schema::hasColumn('cities', 'longitude')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }

        // Ajouter colonnes manquantes à offers
        if (!Schema::hasColumn('offers', 'slug')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->string('slug')->unique()->after('title');
            });
        }
        
        if (!Schema::hasColumn('offers', 'long_description')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->text('long_description')->nullable()->after('description');
            });
        }
        
        if (!Schema::hasColumn('offers', 'cover_image')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->string('cover_image')->nullable()->after('long_description');
            });
        }
        
        if (!Schema::hasColumn('offers', 'gallery')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->json('gallery')->nullable()->after('cover_image');
            });
        }
        
        if (!Schema::hasColumn('offers', 'included_items')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->json('included_items')->nullable()->after('gallery');
            });
        }
        
        if (!Schema::hasColumn('offers', 'excluded_items')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->json('excluded_items')->nullable()->after('included_items');
            });
        }
        
        if (!Schema::hasColumn('offers', 'difficulty_level')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->enum('difficulty_level', ['easy', 'moderate', 'challenging', 'expert'])->default('easy')->after('excluded_items');
            });
        }
        
        if (!Schema::hasColumn('offers', 'min_age')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->integer('min_age')->default(0)->after('difficulty_level');
            });
        }
        
        if (!Schema::hasColumn('offers', 'status')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('min_age');
            });
        }
        
        if (!Schema::hasColumn('offers', 'is_featured')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->after('status');
            });
        }
        
        if (!Schema::hasColumn('offers', 'is_instant_booking')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->boolean('is_instant_booking')->default(false)->after('is_featured');
            });
        }
        
        if (!Schema::hasColumn('offers', 'available_spots')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->integer('available_spots')->nullable()->after('is_instant_booking');
            });
        }
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['slug', 'code', 'continent']);
        });
        
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['slug', 'latitude', 'longitude']);
        });
        
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'long_description', 'cover_image', 'gallery',
                'included_items', 'excluded_items', 'difficulty_level',
                'min_age', 'status', 'is_featured', 'is_instant_booking',
                'available_spots'
            ]);
        });
    }
};