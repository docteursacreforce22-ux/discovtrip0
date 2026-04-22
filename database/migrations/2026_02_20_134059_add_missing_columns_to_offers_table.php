<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'long_description'))
                $table->longText('long_description')->nullable()->after('description');

            if (!Schema::hasColumn('offers', 'cover_image'))
                $table->string('cover_image')->nullable()->after('long_description');

            if (!Schema::hasColumn('offers', 'gallery'))
                $table->json('gallery')->nullable()->after('cover_image');

            if (!Schema::hasColumn('offers', 'discount_percentage'))
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('base_price');

            if (!Schema::hasColumn('offers', 'min_age'))
                $table->integer('min_age')->nullable()->after('max_participants');

            if (!Schema::hasColumn('offers', 'difficulty_level'))
                $table->string('difficulty_level')->default('easy')->after('min_age');

            if (!Schema::hasColumn('offers', 'included_items'))
                $table->json('included_items')->nullable()->after('difficulty_level');

            if (!Schema::hasColumn('offers', 'excluded_items'))
                $table->json('excluded_items')->nullable()->after('included_items');

            if (!Schema::hasColumn('offers', 'is_instant_booking'))
                $table->boolean('is_instant_booking')->default(false)->after('is_featured');

            if (!Schema::hasColumn('offers', 'available_spots'))
                $table->integer('available_spots')->nullable()->after('is_instant_booking');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'long_description', 'cover_image', 'gallery',
                'discount_percentage', 'min_age', 'difficulty_level',
                'included_items', 'excluded_items',
                'is_instant_booking', 'available_spots',
            ]);
        });
    }
};