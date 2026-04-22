<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Transaction
            $table->string('transaction_id')->unique();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Amount
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            
            // Gateway
            $table->enum('gateway', ['stripe', 'fedapay', 'paypal', 'wave']);
            $table->enum('method', ['card', 'mobile_money', 'bank_transfer', 'paypal']);
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            
            // Gateway Data
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_customer_id')->nullable();
            $table->json('gateway_metadata')->nullable();
            
            // Refund
            $table->decimal('refunded_amount', 10, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            
            // Failure
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_id');
            $table->index('booking_id');
            $table->index('status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
