<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'slug'))
                $table->string('slug')->nullable()->unique()->after('name');
            if (!Schema::hasColumn('cities', 'description'))
                $table->text('description')->nullable()->after('slug');
            if (!Schema::hasColumn('cities', 'cover_image'))
                $table->string('cover_image')->nullable()->after('description');
            if (!Schema::hasColumn('cities', 'region'))
                $table->string('region')->nullable()->after('cover_image');
            if (!Schema::hasColumn('cities', 'is_featured'))
                $table->boolean('is_featured')->default(false)->after('region');
            if (!Schema::hasColumn('cities', 'is_active'))
                $table->boolean('is_active')->default(true)->after('is_featured');
        });
    }
    public function down(): void {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['slug','description','cover_image','region','is_featured','is_active']);
        });
    }
};