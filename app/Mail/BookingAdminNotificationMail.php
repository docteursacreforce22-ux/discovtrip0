<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingAdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $prefix = match($this->booking->status) {
            'cancelled_by_user' => '❌ Annulation',
            'confirmed'         => '✅ Confirmée',
            default             => '🔔 Nouvelle réservation',
        };

        return new Envelope(
            subject: "{$prefix} #{$this->booking->reference} — DiscovTrip"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-admin-notification');
    }
}