<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Utilisé par SendBookingReminders pour ne pas renvoyer deux fois le rappel
            // hasColumn checks : reminder_sent et reminder_sent_at sont déjà présents
            // dans create_bookings_table — cette migration est idempotente
            if (! Schema::hasColumn('bookings', 'reminder_sent')) {
                $table->boolean('reminder_sent')->default(false)->after('notes');
            }
            if (! Schema::hasColumn('bookings', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('reminder_sent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'reminder_sent_at')) {
                $table->dropColumn('reminder_sent_at');
            }
            if (Schema::hasColumn('bookings', 'reminder_sent')) {
                $table->dropColumn('reminder_sent');
            }
        });
    }
};