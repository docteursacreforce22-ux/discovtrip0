<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('title', 200);
            $table->text('description');
            $table->string('short_description', 160)->nullable();
            $table->string('slug')->unique();
            
            // Classification
            $table->enum('category', ['tour', 'activity', 'excursion', 'workshop', 'cultural', 'adventure', 'gastronomy', 'wellness', 'sport', 'nature', 'water_sports', 'nightlife', 'shopping', 'family']);
            $table->enum('status', ['draft', 'pending_review', 'published', 'paused', 'archived', 'rejected'])->default('draft');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            
            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->string('currency', 3)->default('XOF');
            $table->decimal('promotional_price', 10, 2)->nullable();
            $table->timestamp('promotion_starts_at')->nullable();
            $table->timestamp('promotion_ends_at')->nullable();
            $table->boolean('is_price_per_person')->default(true);
            
            // Duration & Capacity
            $table->integer('duration_minutes');
            $table->integer('min_participants')->default(1);
            $table->integer('max_participants');
            
            // Details
            $table->json('languages')->nullable(); // ['fr', 'en']
            $table->json('included')->nullable();
            $table->json('excluded')->nullable();
            $table->json('requirements')->nullable();
            $table->string('meeting_point')->nullable();
            
            // Availability
            $table->boolean('is_available_all_year')->default(true);
            $table->json('available_days_of_week')->nullable();
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->integer('min_advance_hours')->default(24);
            $table->integer('max_advance_days')->default(365);
            
            // Media
            $table->json('images')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('videos')->nullable();
            
            // Reviews
            $table->decimal('average_rating', 3, 1)->default(0);
            $table->integer('reviews_count')->default(0);
            
            // SEO
            $table->string('meta_title', 60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->json('keywords')->nullable();
            $table->integer('views_count')->default(0);
            
            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_instant_booking')->default(false);
            $table->boolean('cancellation_allowed')->default(true);
            
            // Stats
            $table->integer('bookings_count')->default(0);
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'city_id']);
            $table->index('category');
            $table->index('is_featured');
            $table->index('average_rating');
        });
    }

    public function down(): void {
        Schema::dropIfExists('offers');
    }
};
