<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $bookingUrl;

    public function __construct(public Booking $booking)
    {
        // Résoudre le prénom — fonctionne pour membres ET invités
        $this->clientName = $booking->guest_first_name
            ?? optional($booking->user)->first_name
            ?? optional($booking->user)->name
            ?? 'Voyageur';

        // URL de la réservation — signée pour les invités
        $this->bookingUrl = is_null($booking->user_id)
            ? \Illuminate\Support\Facades\URL::signedRoute(
                'bookings.show',
                ['reference' => $booking->reference]
              )
            : route('bookings.show', $booking->reference);
    }

    public function envelope(): Envelope
    {
        $subject = $this->booking->status === 'confirmed'
            ? '✅ Réservation confirmée #' . $this->booking->reference . ' — DiscovTrip'
            : '📋 Demande reçue #' . $this->booking->reference . ' — DiscovTrip';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        // CORRECTION : le fichier est booking-confirmed, pas booking-confirmation
        return new Content(view: 'emails.booking-confirmed');
    }
}
