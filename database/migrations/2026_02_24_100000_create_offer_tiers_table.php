<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')
                  ->constrained('offers')
                  ->cascadeOnDelete();

            // ── Identité du tier
            // 'discovery' | 'comfort' | 'exception'
            $table->string('type', 20);
            $table->string('label');           // ex: "Découverte", "Confort", "Exception"
            $table->string('tagline')->nullable(); // ex: "L'essentiel de l'expérience"

            // ── Tarif
            $table->decimal('price', 10, 2);
            $table->boolean('price_is_indicative')->default(false);
            // Si true → affiche "à partir de X FCFA" + bouton WhatsApp
            // Si false → prix fixe, réservation directe possible

            $table->string('currency', 10)->default('XOF');

            // ── Contenu
            $table->text('description')->nullable();
            $table->json('included_items')->nullable();   // ce qui est inclus dans CE tier
            $table->json('excluded_items')->nullable();   // ce qui n'est pas inclus

            // ── Comportement réservation
            $table->boolean('whatsapp_only')->default(false);
            // Exception → whatsapp_only = true (finalisation humaine obligatoire)

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            // 0 = Découverte, 1 = Confort, 2 = Exception

            $table->timestamps();

            // Un seul tier de chaque type par offre
            $table->unique(['offer_id', 'type']);
        });

        // Ajouter colonne tier_id dans bookings pour savoir quel tier a été réservé
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'offer_tier_id')) {
                $table->foreignId('offer_tier_id')
                      ->nullable()
                      ->after('offer_id')
                      ->constrained('offer_tiers')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offer_tier_id');
        });

        Schema::dropIfExists('offer_tiers');
    }
};