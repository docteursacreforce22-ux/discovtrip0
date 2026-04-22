<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotlights', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('badge_text')->default('Patrimoine Béninois');
            $table->string('badge_icon')->default('fa-crown');
            $table->string('highlight_word')->nullable()->comment('Mot mis en évidence dans le titre');
            // 3 stats configurables
            $table->string('stat1_value')->nullable();
            $table->string('stat1_label')->nullable();
            $table->string('stat2_value')->nullable();
            $table->string('stat2_label')->nullable();
            $table->string('stat3_value')->nullable();
            $table->string('stat3_label')->nullable();
            // CTAs
            $table->string('cta1_label')->nullable();
            $table->string('cta1_url')->nullable();
            $table->string('cta2_label')->nullable();
            $table->string('cta2_url')->nullable();
            // Contrôle affichage
            $table->boolean('is_active')->default(false);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotlights');
    }
};