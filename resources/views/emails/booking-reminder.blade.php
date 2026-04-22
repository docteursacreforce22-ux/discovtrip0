<x-emails.layout
    title="Votre expérience demain — {{ $booking->offer->title }}"
    preheader="⏰ Rappel : votre expérience {{ $booking->offer->title }} a lieu demain ! Tout ce qu'il faut savoir.">

    {{-- Hero --}}
    <div class="email-hero" style="background: linear-gradient(135deg, #1a3d6e 0%, #0d2a4a 100%);">
        <div style="font-size:44px; margin-bottom:16px;">⏰</div>
        <h1 class="email-hero-title">
            C'est demain !<br><em>Êtes-vous prêt ?</em>
        </h1>
        <p class="email-hero-sub">
            Votre expérience {{ $booking->offer->title }} a lieu demain.
            Voici tout ce qu'il faut savoir.
        </p>
    </div>

    {{-- Body --}}
    <div class="email-body">
        <p class="email-greeting">Bonjour {{ $booking->guest_first_name ?? optional($booking->user)->first_name ?? "Voyageur" }},</p>
        <p class="email-p">
            Votre expérience est dans <strong>moins de 24h</strong> ! Nous vous rappelons
            les informations essentielles pour que tout se passe parfaitement.
        </p>

        {{-- Récap expérience --}}
        <div class="email-card">
            <div class="email-card-title">📍 Votre expérience de demain</div>
            <div class="email-row">
                <span class="email-row-label">Expérience</span>
                <span class="email-row-value">{{ $booking->offer->title }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Référence</span>
                <span class="email-row-value" style="font-family:monospace;color:#D4A20F;">{{ $booking->reference }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Date</span>
                <span class="email-row-value">{{ $booking->booking_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            </div>
            @if($booking->booking_time)
            <div class="email-row">
                <span class="email-row-label">Heure de départ</span>
                <span class="email-row-value" style="color:#D4A20F; font-size:16px; font-weight:800;">{{ $booking->booking_time }}</span>
            </div>
            @endif
            <div class="email-row">
                <span class="email-row-label">Lieu de rendez-vous</span>
                <span class="email-row-value">{{ $booking->offer->meeting_point ?? $booking->offer->city->name ?? 'Voir les détails' }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Participants</span>
                <span class="email-row-value">{{ $booking->participants }} personne{{ $booking->participants > 1 ? 's' : '' }}</span>
            </div>
        </div>

        {{-- Checklist --}}
        <div class="email-card" style="border-left-color: #1F6B44;">
            <div class="email-card-title" style="color:#1F6B44;">✅ Checklist avant de partir</div>
            @foreach([
                'Votre confirmation de réservation (référence : '.$booking->reference.')',
                'Une pièce d\'identité valide',
                'Tenue adaptée à l\'activité (voir description de l\'expérience)',
                'De l\'eau et une protection solaire si activité en plein air',
                'Votre téléphone chargé pour contacter le guide si besoin',
            ] as $item)
            <div class="email-row" style="border-color:#c8e8d0;">
                <span style="color:#1F6B44; font-size:14px;">☑️ {{ $item }}</span>
            </div>
            @endforeach
        </div>

        {{-- Alerte annulation dernière minute --}}
        <div class="email-alert email-alert--amber">
            ⚠️ <strong>Annulation de dernière minute :</strong> Passé ce délai, l'annulation entraîne
            des frais conformément à notre
            <a href="{{ route('cancellation') }}" style="color:#8a6a00;font-weight:600;">politique d'annulation</a>.
        </div>

        <div class="email-cta-wrap">
            <a href="{{ route('account.bookings') }}" class="email-cta">
                📋 Voir les détails de ma réservation
            </a>
        </div>

        <div class="email-divider"></div>

        <p class="email-p" style="font-size:13px; color:#9a8a78; text-align:center;">
            Un problème de dernière minute ?
            <a href="https://wa.me/22901000000" style="color:#c49a0d;font-weight:600;">WhatsApp</a>
            &nbsp;·&nbsp;
            <a href="{{ route('contact') }}" style="color:#c49a0d;font-weight:600;">Contact</a>
        </p>
    </div>

</x-emails.layout>