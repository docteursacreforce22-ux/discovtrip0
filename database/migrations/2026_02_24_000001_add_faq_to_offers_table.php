<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // FAQ par offre — tableau JSON [{q:'...', r:'...'}]
            // Affiché avant les FAQs génériques sur la page détail
            if (! Schema::hasColumn('offers', 'faq')) {
                $table->json('faq')->nullable()->after('excluded_items');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumnIfExists('faq');
        });
    }
};