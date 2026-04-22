<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'guest_first_name')) {
                $table->string('guest_first_name')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('bookings', 'guest_last_name')) {
                $table->string('guest_last_name')->nullable()->after('guest_first_name');
            }
            if (! Schema::hasColumn('bookings', 'guest_email')) {
                $table->string('guest_email')->nullable()->after('guest_last_name');
            }
            if (! Schema::hasColumn('bookings', 'guest_phone')) {
                $table->string('guest_phone', 30)->nullable()->after('guest_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumnIfExists('guest_first_name');
            $table->dropColumnIfExists('guest_last_name');
            $table->dropColumnIfExists('guest_email');
            $table->dropColumnIfExists('guest_phone');
        });
    }
};