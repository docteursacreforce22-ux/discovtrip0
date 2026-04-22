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
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('payment_mode', [
                'on_site',   // Payer sur place uniquement
                'online',    // Paiement en ligne uniquement
                'both',      // Les deux au choix
            ])->default('on_site')->after('is_instant_booking');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
        });
    }
};
