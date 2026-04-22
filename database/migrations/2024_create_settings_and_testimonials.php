<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pour settings globaux du site
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Table pour témoignages
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_title')->nullable(); // Ex: "Voyageur de Paris"
            $table->string('client_photo')->nullable();
            $table->text('testimonial');
            $table->integer('rating')->default(5); // 1-5 étoiles
            $table->string('offer_title')->nullable(); // Offre concernée
            $table->date('travel_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('site_settings');
    }
};