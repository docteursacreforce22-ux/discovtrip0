<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Infrastructure\Services\StripeService;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripeService $stripeService)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        if (!$signature || !$payload) {
            Log::warning('Stripe webhook missing signature or payload', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Invalid webhook request'], 400);
        }

        $event = $this->stripeService->constructWebhookEvent($payload, $signature);

        if ($event === null) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', [
            'id' => $event->id,
            'type' => $event->type,
        ]);

        // Mise à jour minimale et idempotente des paiements / réservations
        $this->syncPaymentState($event);

        return response()->json(['received' => true]);
    }

    /**
     * Synchronise l'état local en fonction de certains événements Stripe.
     * Implémentation volontairement conservatrice et idempotente.
     */
    protected function syncPaymentState(\Stripe\Event $event): void
    {
        $object = $event->data['object'] ?? null;

        if (!is_object($object) || !property_exists($object, 'id')) {
            return;
        }

        $stripeId = $object->id;

        // On part du principe que gateway_payment_id contient l'ID Stripe (charge ou payment_intent)
        $payment = Payment::where('gateway_payment_id', $stripeId)->first();

        if (!$payment) {
            return;
        }

        // Selon le type d'événement, on ajuste l'état local sans changer la logique métier Domain
        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'charge.succeeded':
                $payment->status = 'succeeded';
                $payment->failed_at = null;
                $payment->save();

                if ($payment->booking && !$payment->booking->is_paid) {
                    $payment->booking->update([
                        'is_paid' => true,
                        'paid_at' => now(),
                        'status' => $payment->booking->status === 'pending'
                            ? 'confirmed'
                            : $payment->booking->status,
                    ]);
                }
                break;

            case 'payment_intent.payment_failed':
            case 'charge.failed':
                $payment->status = 'failed';
                $payment->failed_at = now();
                $payment->save();
                break;

            case 'charge.refunded':
            case 'charge.refund.updated':
                $payment->status = 'refunded';
                $payment->refunded_at = now();
                $payment->refunded_amount = $payment->amount;
                $payment->save();

                if ($payment->booking && !$payment->booking->refunded_at) {
                    $payment->booking->update([
                        'status' => 'refunded',
                        'refunded_at' => now(),
                    ]);
                }
                break;
        }
    }
}

