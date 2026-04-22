<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Review
            $table->integer('rating'); // 1-5
            $table->text('comment');
            
            // Moderation
            $table->enum('status', ['pending', 'published', 'rejected', 'spam'])->default('pending');
            $table->text('moderator_notes')->nullable();
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['offer_id', 'status']);
            $table->index('booking_id');
            $table->unique('booking_id'); // One review per booking
        });
    }

    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
