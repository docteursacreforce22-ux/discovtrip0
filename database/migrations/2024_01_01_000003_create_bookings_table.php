<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Reference
            $table->string('reference', 20)->unique(); // BK-YYYYMMDD-XXXXX
            
            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            
            // Booking Details
            $table->timestamp('booking_date');
            $table->text('special_requests')->nullable();
            $table->text('customer_notes')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'confirmed', 'processing', 'completed', 'cancelled_by_user', 'cancelled_by_partner', 'cancelled_by_system', 'refunded'])->default('pending');
            
            // Participants
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->json('participant_details')->nullable();
            
            // Pricing
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3);
            $table->enum('cancellation_policy', ['flexible', 'moderate', 'strict', 'very_strict', 'non_refundable'])->default('moderate');
            
            // Payment
            $table->boolean('is_paid')->default(false);
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            
            // Cancellation
            $table->decimal('refunded_amount', 10, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable();
            
            // Notifications
            $table->boolean('confirmation_sent')->default(false);
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            
            // Expiration
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('reference');
            $table->index(['user_id', 'status']);
            $table->index('booking_date');
            $table->index('status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
