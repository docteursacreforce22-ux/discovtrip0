<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            $table->index(['country_id', 'name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('cities');
    }
};
