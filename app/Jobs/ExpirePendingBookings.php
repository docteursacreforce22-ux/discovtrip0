<?php
declare(strict_types=1);
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use App\Models\Booking;

class ExpirePendingBookings implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void {
        $expiredBookings = Booking::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'cancelled_by_system',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Expiration délai de paiement',
            ]);

            \Log::info("Booking {$booking->reference} expired and cancelled");
        }
    }
}
