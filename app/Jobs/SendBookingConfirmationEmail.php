<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $bookingId) {}

    public function handle(): void
    {
        $booking = Booking::with(['offer.city', 'tier'])->find($this->bookingId);

        if (! $booking) {
            Log::warning("SendBookingConfirmationEmail: booking #{$this->bookingId} introuvable.");
            return;
        }

        // Destinataire : utilisateur connecté ou invité
        $email = $booking->user?->email ?? $booking->guest_email;

        if (! $email) {
            Log::warning("SendBookingConfirmationEmail: aucun email pour booking #{$this->bookingId}.");
            return;
        }

        Mail::to($email)->send(new BookingConfirmationMail($booking));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendBookingConfirmationEmail: échec pour booking #{$this->bookingId}.", [
            'error' => $exception->getMessage(),
        ]);
    }
}