@php
    // Résolution sécurisée — fonctionne pour membres ET invités
    $clientName = $clientName
        ?? ($booking->guest_first_name
            ?? optional($booking->user)->first_name
            ?? optional($booking->user)->name
            ?? 'Voyageur');
    $bookingUrl = $bookingUrl
        ?? (is_null($booking->user_id)
            ? \Illuminate\Support\Facades\URL::signedRoute('bookings.show', ['reference' => $booking->reference])
            : route('bookings.show', $booking->reference));
@endphp

<x-emails.layout
    title="Réservation confirmée #{{ $booking->reference }}"
    preheader="✅ Votre réservation {{ $booking->offer->title }} est confirmée ! Rendez-vous le {{ $booking->booking_date->locale('fr')->isoFormat('D MMMM YYYY') }}.">

    {{-- Hero --}}
    <div class="email-hero">
        <div style="font-size:44px; margin-bottom:16px;">✅</div>
        <h1 class="email-hero-title">
            Réservation<br><em>confirmée !</em>
        </h1>
        <p class="email-hero-sub">
            Votre expérience est réservée. Il ne vous reste qu'à profiter.
        </p>
    </div>

    {{-- Body --}}
    <div class="email-body">
        <p class="email-greeting">Bonjour {{ $clientName }},</p>
        <p class="email-p">
            Votre réservation a bien été enregistrée et confirmée.
            Voici le récapitulatif de votre expérience :
        </p>

        {{-- Récap réservation --}}
        <div class="email-card">
            <div class="email-card-title">📋 Récapitulatif de réservation</div>
            <div class="email-row">
                <span class="email-row-label">Référence</span>
                <span class="email-row-value" style="color:#D4A20F; font-family: monospace;">{{ $booking->reference }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Expérience</span>
                <span class="email-row-value">{{ $booking->offer->title }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Destination</span>
                <span class="email-row-value">{{ $booking->offer->city->name ?? 'Bénin' }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Date</span>
                <span class="email-row-value">{{ $booking->booking_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            </div>
            @if($booking->booking_time)
            <div class="email-row">
                <span class="email-row-label">Heure</span>
                <span class="email-row-value">{{ $booking->booking_time }}</span>
            </div>
            @endif
            <div class="email-row">
                <span class="email-row-label">Participants</span>
                <span class="email-row-value">{{ $booking->participants }} personne{{ $booking->participants > 1 ? 's' : '' }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Montant total</span>
                <span class="email-row-value" style="color:#D4A20F; font-size:16px;">
                    {{ number_format($booking->total_price, 0, ',', ' ') }} FCFA
                </span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Statut</span>
                <span class="email-row-value">
                    <span style="background:rgba(42,143,94,.12);color:#1F6B44;padding:3px 10px;border-radius:50px;font-size:12px;">
                        ✓ Confirmée
                    </span>
                </span>
            </div>
        </div>

        {{-- Alerte annulation --}}
        <div class="email-alert email-alert--amber">
            🕐 <strong>Annulation gratuite</strong> jusqu'au
            {{ $booking->booking_date->subHours(48)->locale('fr')->isoFormat('D MMMM YYYY [à] HH[h]mm') }}.
            Passé ce délai, des frais s'appliquent.
        </div>

        <div class="email-cta-wrap">
            <a href="{{ $bookingUrl }}" class="email-cta">
                📋 Voir ma réservation
            </a>
        </div>

        <div class="email-divider"></div>

        <p class="email-p" style="font-size:13px; color:#9a8a78;">
            Besoin de modifier ou d'annuler votre réservation ?
            Connectez-vous à votre <a href="{{ $bookingUrl }}" style="color:#c49a0d;font-weight:600;">voir ma réservation</a>
            ou <a href="{{ route('contact') }}" style="color:#c49a0d;font-weight:600;">contactez-nous</a>.
        </p>
    </div>

</x-emails.layout>