<x-emails.layout
    title="Réservation annulée #{{ $booking->reference }}"
    preheader="Votre réservation {{ $booking->offer->title }} a été annulée. Informations sur votre remboursement.">

    {{-- Hero --}}
    <div class="email-hero" style="background: linear-gradient(135deg, #2a1a12 0%, #5a2a18 100%);">
        <div style="font-size:44px; margin-bottom:16px;">❌</div>
        <h1 class="email-hero-title">
            Réservation<br><em>annulée</em>
        </h1>
        <p class="email-hero-sub">
            Votre réservation a bien été annulée. Voici les détails.
        </p>
    </div>

    {{-- Body --}}
    <div class="email-body">
        @php
            $clientName = $clientName
                ?? ($booking->guest_first_name
                    ?? optional($booking->user)->first_name
                    ?? optional($booking->user)->name
                    ?? 'Voyageur');
        @endphp
        <p class="email-greeting">Bonjour {{ $clientName }},</p>
        <p class="email-p">
            Nous confirmons l'annulation de votre réservation.
            Voici le récapitulatif :
        </p>

        {{-- Récap --}}
        <div class="email-card" style="border-left-color: #c93923;">
            <div class="email-card-title" style="color:#c93923;">📋 Réservation annulée</div>
            <div class="email-row">
                <span class="email-row-label">Référence</span>
                <span class="email-row-value" style="font-family:monospace;">{{ $booking->reference }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Expérience</span>
                <span class="email-row-value">{{ $booking->offer->title }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Date prévue</span>
                <span class="email-row-value">{{ $booking->booking_date->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Montant payé</span>
                <span class="email-row-value">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Annulée par</span>
                <span class="email-row-value">
                    {{ $booking->cancelled_by === 'user' ? 'Vous' : 'DiscovTrip' }}
                </span>
            </div>
        </div>

        {{-- Remboursement --}}
        @php
            $hoursBeforeBooking = now()->diffInHours($booking->booking_date, false);
            $refundRate = $hoursBeforeBooking >= 48 ? 100 : ($hoursBeforeBooking >= 24 ? 50 : 0);
            $refundAmount = ($booking->total_price * $refundRate) / 100;
        @endphp

        @if($booking->cancelled_by === 'admin' || $booking->cancelled_by === 'guide')
        <div class="email-alert email-alert--green">
            💚 <strong>Remboursement intégral en cours.</strong>
            Le guide ou DiscovTrip est à l'origine de cette annulation.
            Vous serez remboursé de <strong>{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</strong> sous 48h.
        </div>
        @elseif($refundRate === 100)
        <div class="email-alert email-alert--green">
            💚 <strong>Remboursement intégral.</strong>
            Annulation effectuée plus de 48h avant l'expérience.
            Vous serez remboursé de <strong>{{ number_format($refundAmount, 0, ',', ' ') }} FCFA</strong> sous 5 à 10 jours ouvrés.
        </div>
        @elseif($refundRate === 50)
        <div class="email-alert email-alert--amber">
            ⚠️ <strong>Remboursement partiel (50%).</strong>
            Annulation entre 24h et 48h avant l'expérience.
            Vous serez remboursé de <strong>{{ number_format($refundAmount, 0, ',', ' ') }} FCFA</strong> sous 5 à 10 jours ouvrés.
        </div>
        @else
        <div class="email-alert email-alert--red">
            ❌ <strong>Aucun remboursement.</strong>
            Annulation effectuée moins de 24h avant l'expérience.
            Consultez notre <a href="{{ route('cancellation') }}" style="color:#b03520;font-weight:600;">politique d'annulation</a>.
        </div>
        @endif

        <div class="email-cta-wrap">
            <a href="{{ route('destinations') }}" class="email-cta">
                🗺️ Découvrir d'autres expériences
            </a>
        </div>

        <div class="email-divider"></div>

        <p class="email-p" style="font-size:13px; color:#9a8a78;">
            Une question sur votre remboursement ?
            <a href="{{ route('contact') }}" style="color:#c49a0d;font-weight:600;">Contactez-nous</a>
            ou consultez notre
            <a href="{{ route('cancellation') }}" style="color:#c49a0d;font-weight:600;">politique d'annulation</a>.
        </p>
    </div>

</x-emails.layout>