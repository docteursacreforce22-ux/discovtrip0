<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature   = 'bookings:send-reminders {--dry-run : Afficher sans envoyer}';
    protected $description = 'Envoie les rappels J-1 aux clients ayant une réservation demain';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();
        $isDry    = $this->option('dry-run');

        if ($isDry) {
            $this->warn('Mode dry-run — aucun email ne sera envoyé.');
        }

        $bookings = Booking::whereDate('booking_date', $tomorrow)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('reminder_sent', false)
            ->with(['offer.city', 'user'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('Aucun rappel à envoyer pour demain.');
            return self::SUCCESS;
        }

        $this->info("📨 {$bookings->count()} rappel(s) à envoyer...");

        $sent   = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            // CORRECTION : utiliser l'accessor client_email du modèle Booking
            $email = $booking->client_email;

            if (! $email) {
                $this->warn("⚠️  Pas d'email pour réservation #{$booking->reference}");
                continue;
            }

            if ($isDry) {
                $this->line("  [dry-run] → {$email} ({$booking->reference})");
                $sent++;
                continue;
            }

            try {
                Mail::to($email)->queue(new BookingReminderMail($booking));

                $booking->update([
                    'reminder_sent'    => true,
                    'reminder_sent_at' => now(),
                ]);

                $this->line("  ✅ → {$email} ({$booking->reference})");
                $sent++;

            } catch (\Exception $e) {
                Log::error("SendBookingReminders: échec pour {$booking->reference}: " . $e->getMessage());
                $this->error("  ❌ Échec pour {$booking->reference}: " . $e->getMessage());
                $failed++;
            }
        }

        $suffix = $isDry ? ' (dry-run)' : '';
        $this->info("Terminé{$suffix} : {$sent} rappels envoyés, {$failed} échecs.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}