<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes de paiement sur bookings.
     * Ces colonnes existent dans la DB locale mais n'avaient pas
     * de migration dédiée dans le projet — corrigé ici pour Railway.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('is_paid')
                      ->comment('fedapay | stripe | on_site');
            }
            if (! Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->nullable()->default('pending')->after('payment_method')
                      ->comment('pending | paid | failed | refunded');
            }
            if (! Schema::hasColumn('bookings', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_status')
                      ->comment('ID transaction côté gateway');
            }
            if (! Schema::hasColumn('bookings', 'payment_transaction_id')) {
                $table->string('payment_transaction_id')->nullable()->after('payment_reference')
                      ->comment('ID payment_intent Stripe ou transaction FedaPay');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $cols = ['payment_method', 'payment_status', 'payment_reference', 'payment_transaction_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};