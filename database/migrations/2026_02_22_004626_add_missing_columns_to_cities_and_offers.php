<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cities
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('country_id');
            }
            if (!Schema::hasColumn('cities', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_featured');
            }
            if (!Schema::hasColumn('cities', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('is_active');
            }
        });

        // Offers
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_featured');
            }
            if (!Schema::hasColumn('offers', 'available_spots')) {
                $table->integer('available_spots')->nullable()->after('max_participants');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_active', 'cover_image']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'available_spots']);
        });
    }
};