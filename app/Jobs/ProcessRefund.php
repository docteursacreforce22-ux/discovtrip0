<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\BookingCancelledMail;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int    $bookingId,
        public readonly float  $refundAmount,
        public readonly string $reason = ''
    ) {}

    public function handle(): void
    {
        $booking = Booking::with(['user', 'payments'])->find($this->bookingId);

        if (! $booking) {
            Log::warning("ProcessRefund: booking #{$this->bookingId} introuvable.");
            return;
        }

        // Trouver le paiement réussi
        $payment = Payment::where('booking_id', $this->bookingId)
            ->where('status', 'succeeded')
            ->latest()
            ->first();

        if (! $payment) {
            Log::warning("ProcessRefund: aucun paiement réussi pour booking #{$this->bookingId}.");
            return;
        }

        // ── Remboursement via Stripe ──────────────────────────────────
        if ($payment->gateway === 'stripe' && $payment->gateway_payment_id) {
            $this->processStripeRefund($payment, $booking);
            return;
        }

        // ── Remboursement manuel (FedaPay, paiement sur place) ────────
        // Marquer comme remboursé manuellement et notifier l'admin
        $this->processManualRefund($payment, $booking);
    }

    private function processStripeRefund(Payment $payment, Booking $booking): void
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));

            $amountCents = (int) round($this->refundAmount * 100);

            \Stripe\Refund::create([
                'payment_intent' => $payment->gateway_payment_id,
                'amount'         => $amountCents,
                'reason'         => 'requested_by_customer',
                'metadata'       => [
                    'booking_reference' => $booking->reference,
                    'reason'            => $this->reason,
                ],
            ]);

            $this->markRefunded($payment, $booking);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error("ProcessRefund Stripe: échec pour booking #{$this->bookingId}.", [
                'error'   => $e->getMessage(),
                'booking' => $booking->reference,
            ]);
            throw $e; // Permet les retries automatiques
        }
    }

    private function processManualRefund(Payment $payment, Booking $booking): void
    {
        // Pour les paiements non-Stripe : marquer comme remboursé
        // et laisser l'admin effectuer le virement manuellement
        $this->markRefunded($payment, $booking);

        Log::info("ProcessRefund manuel requis pour booking #{$this->bookingId}.", [
            'gateway'  => $payment->gateway,
            'amount'   => $this->refundAmount,
            'currency' => $payment->currency,
            'reason'   => $this->reason,
        ]);
    }

    private function markRefunded(Payment $payment, Booking $booking): void
    {
        $payment->update([
            'status'          => 'refunded',
            'refunded_amount' => $this->refundAmount,
            'refunded_at'     => now(),
        ]);

        $booking->update([
            'status'      => 'refunded',
            'refunded_at' => now(),
        ]);

        // Notifier le client
        $email = $booking->user?->email ?? $booking->guest_email;
        if ($email) {
            try {
                Mail::to($email)->send(new BookingCancelledMail($booking));
            } catch (\Exception $e) {
                Log::warning("ProcessRefund: email refund échoué pour booking #{$this->bookingId}.");
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessRefund: échec définitif pour booking #{$this->bookingId}.", [
            'error' => $exception->getMessage(),
        ]);
    }
}