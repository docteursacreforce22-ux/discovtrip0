<?php
declare(strict_types=1);
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use App\Models\Booking;

class SendDailyReminders implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void {
        $tomorrow = now()->addDay();

        $bookings = Booking::whereIn('status', ['confirmed', 'processing'])
            ->whereBetween('booking_date', [now(), $tomorrow])
            ->where('reminder_sent', false)
            ->get();

        foreach ($bookings as $booking) {
            SendBookingReminderEmail::dispatch($booking->id);
        }

        \Log::info("Dispatched {$bookings->count()} booking reminder emails");
    }
}
