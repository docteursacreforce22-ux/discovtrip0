<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $bookingUrl;

    public function __construct(public Booking $booking)
    {
        $this->clientName = $booking->guest_first_name
            ?? optional($booking->user)->first_name
            ?? optional($booking->user)->name
            ?? 'Voyageur';

        $this->bookingUrl = is_null($booking->user_id)
            ? \Illuminate\Support\Facades\URL::signedRoute(
                'bookings.show',
                ['reference' => $booking->reference]
              )
            : route('bookings.show', $booking->reference);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Réservation annulée #' . $this->booking->reference . ' — DiscovTrip'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-cancelled');
    }
}
