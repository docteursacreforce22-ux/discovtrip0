<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    // ══════════════════════════════════════════════════════
    // PAGE CHOIX PAIEMENT
    // ══════════════════════════════════════════════════════

    public function show(Request $request, string $reference)
    {
        $booking = $this->resolveBooking($reference, $request);

        if ($booking->is_paid || $booking->payment_status === 'paid') {
            return redirect($this->bookingUrl($booking))
                ->with('success', 'Cette réservation est déjà réglée. ✓');
        }

        if (in_array($booking->status, ['cancelled_by_user', 'cancelled_by_partner'])) {
            return redirect()->route('offers.index')
                ->with('error', 'Cette réservation a été annulée.');
        }

        $kkiapayEnabled = ! empty(config('services.kkiapay.public_key'));

        return view('pages.bookings.payment', compact('booking', 'kkiapayEnabled'));
    }

    // ══════════════════════════════════════════════════════
    // KKIAPAY — CALLBACK (après paiement réussi)
    // KKiaPay redirige ici avec ?transaction_id=xxx
    // On vérifie côté serveur avant de confirmer.
    // ══════════════════════════════════════════════════════

    public function callbackKkiapay(Request $request, string $reference)
    {
        $transactionId = $request->query('transaction_id');

        if (! $transactionId) {
            Log::warning('KKiaPay callback sans transaction_id', ['reference' => $reference]);
            return redirect()->route('payment.show', $reference)
                ->with('error', 'Transaction introuvable. Contactez-nous si vous avez été débité.');
        }

        $booking = $this->resolveBooking($reference, $request);

        // Idempotent — si déjà confirmé, rediriger proprement
        if ($booking->is_paid) {
            return redirect($this->bookingUrl($booking))
                ->with('success', '🎉 Votre réservation est confirmée.');
        }

        // Vérification serveur à serveur — indispensable pour éviter la fraude
        if (! $this->verifyKkiapayTransaction($transactionId)) {
            Log::error('KKiaPay : transaction non vérifiée', [
                'reference'      => $reference,
                'transaction_id' => $transactionId,
            ]);
            return redirect()->route('payment.show', $reference)
                ->with('error', 'Paiement non vérifié. Contactez-nous si vous avez été débité.');
        }

        $booking->update([
            'payment_status'         => 'paid',
            'payment_method'         => 'kkiapay',
            'payment_transaction_id' => $transactionId,
            'payment_reference'      => $transactionId,
            'is_paid'                => true,
            'paid_at'                => Carbon::now(),
            'status'                 => 'confirmed',
        ]);

        $this->sendConfirmationEmail($booking);

        Log::info('KKiaPay : paiement confirmé', [
            'reference'      => $reference,
            'transaction_id' => $transactionId,
        ]);

        return redirect($this->bookingUrl($booking))
            ->with('success', '🎉 Paiement reçu ! Votre réservation est confirmée.');
    }

    // ══════════════════════════════════════════════════════
    // KKIAPAY — WEBHOOK (notification serveur asynchrone)
    // Déclenché par KKiaPay même si l'utilisateur ferme le
    // navigateur après paiement. Filet de sécurité.
    // ══════════════════════════════════════════════════════

    public function webhookKkiapay(Request $request)
    {
        $payload = $request->all();

        Log::info('KKiaPay webhook reçu', ['payload' => $payload]);

        $transactionId = $payload['transactionId'] ?? null;
        $status        = $payload['status']        ?? null;

        if (! $transactionId) {
            return response()->json(['error' => 'Missing transactionId'], 400);
        }

        // KKiaPay envoie "SUCCESS" ou "FAILED"
        if ($status !== 'SUCCESS') {
            return response()->json(['status' => 'ignored']);
        }

        // Retrouver la réservation via le transaction_id
        $booking = Booking::where('payment_transaction_id', $transactionId)
            ->orWhere('payment_reference', $transactionId)
            ->first();

        if (! $booking) {
            // Peut arriver si le callback est passé avant le webhook
            Log::warning('KKiaPay webhook : réservation introuvable', ['transactionId' => $transactionId]);
            return response()->json(['status' => 'booking_not_found']);
        }

        if (! $booking->is_paid) {
            $booking->update([
                'payment_status'         => 'paid',
                'payment_method'         => 'kkiapay',
                'payment_transaction_id' => $transactionId,
                'is_paid'                => true,
                'paid_at'                => Carbon::now(),
                'status'                 => 'confirmed',
            ]);

            $booking->load(['offer.city', 'tier', 'user']);
            $this->sendConfirmationEmail($booking);

            Log::info('KKiaPay webhook : réservation confirmée via webhook', ['reference' => $booking->reference]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ══════════════════════════════════════════════════════
    // PAIEMENT SUR PLACE — confirmation sans paiement en ligne
    // ══════════════════════════════════════════════════════

    public function confirmOnSite(Request $request, string $reference)
    {
        $booking = $this->resolveBooking($reference, $request);

        if ($booking->is_paid) {
            return redirect($this->bookingUrl($booking))
                ->with('success', 'Réservation déjà confirmée.');
        }

        $offerPayMode = $booking->offer->payment_mode ?? 'both';
        if (! in_array($offerPayMode, ['on_site', 'both'])) {
            return redirect()->route('payment.show', $reference)
                ->with('error', 'Le paiement sur place n\'est pas disponible pour cette offre.');
        }

        $booking->update([
            'payment_method' => 'on_site',
            'payment_status' => 'pending',
            'status'         => 'pending',
        ]);

        return redirect($this->bookingUrl($booking))
            ->with('success', 'Réservation enregistrée ! Vous règlerez sur place le jour J.');
    }

    // ══════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════

    /**
     * Vérifie une transaction KKiaPay côté serveur (API REST).
     * Retourne true uniquement si le statut est SUCCESS.
     */
    private function verifyKkiapayTransaction(string $transactionId): bool
    {
        try {
            $isSandbox = config('services.kkiapay.sandbox', true);
            $baseUrl   = $isSandbox
                ? 'https://sandbox-api.kkiapay.me'
                : 'https://api.kkiapay.me';

            $response = Http::timeout(10)
                ->withHeaders([
                    'x-private-key' => config('services.kkiapay.private_key'),
                ])
                ->get("{$baseUrl}/api/v1/transactions/{$transactionId}/status");

            if (! $response->successful()) {
                Log::warning('KKiaPay verify : réponse API non-2xx', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            $data = $response->json();
            return ($data['status'] ?? '') === 'SUCCESS';

        } catch (\Throwable $e) {
            Log::error('KKiaPay verify exception : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Résoudre la réservation — fonctionne pour les connectés ET les invités.
     */
    private function resolveBooking(string $reference, Request $request): Booking
    {
        $query = Booking::where('reference', $reference)
            ->with(['offer.city', 'offer.activeTiers', 'tier', 'user']);

        if (auth()->check()) {
            return $query->where('user_id', auth()->id())->firstOrFail();
        }

        if ($request->hasValidSignature()) {
            return $query->whereNull('user_id')->firstOrFail();
        }

        return $query->firstOrFail();
    }

    /**
     * URL de confirmation — signée pour les invités.
     */
    private function bookingUrl(Booking $booking): string
    {
        if (is_null($booking->user_id)) {
            return URL::signedRoute('bookings.show', ['reference' => $booking->reference]);
        }
        return route('bookings.show', $booking->reference);
    }

    /**
     * Envoyer l'email de confirmation de réservation.
     */
    private function sendConfirmationEmail(Booking $booking): void
    {
        $email = $booking->guest_email ?? optional($booking->user)->email;
        if (! $email) return;

        try {
            Mail::to($email)->send(new BookingConfirmationMail($booking));
        } catch (\Throwable $e) {
            Log::warning('Email confirmation paiement échoué : ' . $e->getMessage());
        }
    }
}
