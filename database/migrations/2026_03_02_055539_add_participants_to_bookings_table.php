<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('participants')->default(1)->after('infants');
            $table->string('booking_time', 10)->nullable()->after('booking_date');
            $table->text('notes')->nullable()->after('special_requests');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['participants', 'booking_time', 'notes']);
        });
    }
};
