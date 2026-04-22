<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $bookingId) {}

    public function handle(): void
    {
        $booking = Booking::with(['offer.city', 'tier'])->find($this->bookingId);

        if (! $booking) {
            Log::warning("SendBookingReminderEmail: booking #{$this->bookingId} introuvable.");
            return;
        }

        // Ne pas renvoyer si déjà envoyé
        if ($booking->reminder_sent) {
            return;
        }

        $email = $booking->user?->email ?? $booking->guest_email;

        if (! $email) {
            Log::warning("SendBookingReminderEmail: aucun email pour booking #{$this->bookingId}.");
            return;
        }

        Mail::to($email)->send(new BookingReminderMail($booking));

        $booking->update([
            'reminder_sent'    => true,
            'reminder_sent_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendBookingReminderEmail: échec pour booking #{$this->bookingId}.", [
            'error' => $exception->getMessage(),
        ]);
    }
}