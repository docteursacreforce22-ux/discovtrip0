@extends('layouts.app')

@section('title', 'Paiement · ' . $booking->reference . ' — DiscovTrip')

@push('meta')
<meta name="robots" content="noindex, nofollow">
@endpush

@push('styles')
    @vite('resources/css/pages/bookings/payment.css')
@endpush

@php
    $clientName = $booking->guest_first_name
        ? trim($booking->guest_first_name . ' ' . $booking->guest_last_name)
        : optional($booking->user)?->first_name ?? 'Client';

    $offerPayMode   = $booking->offer->payment_mode ?? 'both';
    $hasOnline      = in_array($offerPayMode, ['online', 'both']) && $kkiapayEnabled;
    $hasOnSite      = in_array($offerPayMode, ['on_site', 'both']);

    $bookingUrl = is_null($booking->user_id)
        ? \Illuminate\Support\Facades\URL::signedRoute('bookings.show', ['reference' => $booking->reference])
        : route('bookings.show', $booking->reference);

    $tierLabel = $booking->tier?->label ?? null;

    // URL callback KKiaPay — signée pour les invités
    $kkiapayCallback = is_null($booking->user_id)
        ? \Illuminate\Support\Facades\URL::signedRoute('payment.kkiapay.callback', ['reference' => $booking->reference])
        : route('payment.kkiapay.callback', $booking->reference);
@endphp

@section('content')
<div class="py-page">

    {{-- ══ HERO ══════════════════════════════════════════════ --}}
    <div class="py-hero">
        <div class="py-hero-glow" aria-hidden="true"></div>
        <div class="py-hero-inner">
            <div class="py-hero-eyebrow">
                <i class="fas fa-lock" aria-hidden="true"></i>
                Paiement sécurisé SSL
            </div>
            <h1 class="py-hero-title">Finalisez votre<br><em>réservation</em></h1>
            <p class="py-hero-sub">
                Bonjour {{ $clientName }}, choisissez votre mode de paiement pour confirmer votre expérience.
            </p>
            <div class="py-ref-chip">
                <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                Réf. <strong>{{ $booking->reference }}</strong>
            </div>
        </div>
    </div>

    {{-- ══ CONTENU ══════════════════════════════════════════ --}}
    <div class="py-body">
        <div class="dt-container py-grid">

            {{-- ── COLONNE PRINCIPALE ─────────────────────── --}}
            <div class="py-main">

                {{-- Alertes session --}}
                @if(session('error'))
                    <div class="py-alert py-alert--err" role="alert">
                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="py-alert py-alert--info" role="alert">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        {{ session('info') }}
                    </div>
                @endif

                {{-- ── Paiement en ligne via KKiaPay ── --}}
                @if($hasOnline)
                    <section class="py-section" aria-labelledby="py-online-title">
                        <div class="py-section-head">
                            <div class="py-section-label" id="py-online-title">
                                <span class="py-label-bar" aria-hidden="true"></span>
                                Paiement en ligne
                            </div>
                            <p class="py-section-sub">Confirmation immédiate après le paiement</p>
                        </div>

                        <div class="py-methods">

                            {{-- KKiaPay — Mobile Money + Carte --}}
                            {{-- CORRECTION : onclick="launchKkiapay()" au lieu de openKkiapayWidget() --}}
                            {{-- pour éviter le conflit de nom avec la fonction du SDK KKiaPay --}}
                            <button
                                type="button"
                                id="kkiapay-btn"
                                onclick="launchKkiapay()"
                                class="py-method py-method--kkiapay"
                                aria-label="Payer par Mobile Money ou Carte — {{ number_format($booking->total_price, 0, ',', ' ') }} FCFA">

                                <div class="py-method-icon" aria-hidden="true">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="py-method-body">
                                    <div class="py-method-title">Mobile Money · Carte bancaire</div>
                                    <div class="py-method-sub">MTN MoMo · Moov Money · WAVE · Visa · Mastercard</div>
                                    <div class="py-method-badges">
                                        <span class="py-badge py-badge--green">
                                            <i class="fas fa-bolt" aria-hidden="true"></i> Instantané
                                        </span>
                                        <span class="py-badge">🇧🇯 Bénin · Afrique de l'Ouest</span>
                                        <span class="py-badge py-badge--blue">
                                            Sécurisé KKiaPay
                                        </span>
                                    </div>
                                </div>
                                <div class="py-method-amount" aria-hidden="true">
                                    <span class="py-method-price">{{ number_format($booking->total_price, 0, ',', ' ') }}</span>
                                    <span class="py-method-cur">FCFA</span>
                                </div>
                                <div class="py-method-arrow" aria-hidden="true">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </button>

                        </div>
                    </section>
                @endif

                {{-- Séparateur --}}
                @if($hasOnline && $hasOnSite)
                    <div class="py-or" role="separator"><span>ou</span></div>
                @endif

                {{-- ── Paiement sur place ── --}}
                @if($hasOnSite)
                    <section class="py-section" aria-labelledby="py-onsite-title">
                        <div class="py-section-head">
                            <div class="py-section-label" id="py-onsite-title">
                                <span class="py-label-bar" aria-hidden="true"></span>
                                Paiement sur place
                            </div>
                            <p class="py-section-sub">Payez directement au guide le jour de l'expérience</p>
                        </div>

                        <div class="py-onsite">
                            <div class="py-onsite-icon" aria-hidden="true">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="py-onsite-body">
                                <h3 class="py-onsite-title">Règlement le jour J</h3>
                                <p class="py-onsite-text">
                                    Votre réservation sera marquée <strong>en attente</strong> jusqu'à
                                    confirmation par le guide. Vous pourrez régler en espèces (FCFA)
                                    ou via Mobile Money directement sur place.
                                </p>
                                <div class="py-onsite-list">
                                    <span><i class="fas fa-check" aria-hidden="true"></i> Espèces FCFA acceptées</span>
                                    <span><i class="fas fa-check" aria-hidden="true"></i> Mobile Money sur place</span>
                                    <span><i class="fas fa-check" aria-hidden="true"></i> Confirmation sous 24h</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('payment.on_site.confirm', $booking->reference) }}">
                                @csrf
                                <button type="submit" class="py-onsite-btn">
                                    Confirmer sans payer maintenant
                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </section>
                @endif

                {{-- Aucun mode disponible --}}
                @if(!$hasOnline && !$hasOnSite)
                    <div class="py-unavailable" role="alert">
                        <div class="py-unavailable-icon" aria-hidden="true">⏳</div>
                        <h3>Paiement temporairement indisponible</h3>
                        <p>Notre équipe configure les modes de paiement. Contactez-nous pour finaliser votre réservation.</p>
                        <a href="{{ route('contact') }}" class="py-contact-btn">
                            <i class="fas fa-headset" aria-hidden="true"></i>
                            Contacter l'équipe
                        </a>
                    </div>
                @endif

                {{-- Garanties --}}
                <div class="py-guarantees" aria-label="Garanties DiscovTrip">
                    <div class="py-guarantee">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <span>Paiement chiffré SSL 256 bits</span>
                    </div>
                    <div class="py-guarantee">
                        <i class="fas fa-undo" aria-hidden="true"></i>
                        <span>Annulation gratuite jusqu'à 48h avant</span>
                    </div>
                    <div class="py-guarantee">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <span>Données non partagées</span>
                    </div>
                </div>

            </div>

            {{-- ── SIDEBAR ─────────────────────────────────── --}}
            <aside class="py-sidebar">

                <div class="py-recap">
                    <div class="py-recap-head">
                        <i class="fas fa-receipt" aria-hidden="true"></i>
                        Récapitulatif
                    </div>

                    @if($booking->offer->cover_image)
                        <div class="py-recap-img-wrap">
                            <img src="{{ mediaUrl($booking->offer->cover_image) }}"
                                 alt="{{ $booking->offer->title }}"
                                 class="py-recap-img" loading="lazy" width="400" height="260">
                            <div class="py-recap-img-overlay" aria-hidden="true"></div>
                            <div class="py-recap-img-title">{{ $booking->offer->title }}</div>
                        </div>
                    @else
                        <div class="py-recap-img-ph">
                            <span>{{ $booking->offer->city->name ?? 'Bénin' }}</span>
                        </div>
                    @endif

                    <div class="py-recap-body">

                        <div class="py-recap-row">
                            <span class="py-recap-lbl">
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i> Destination
                            </span>
                            <span class="py-recap-val">{{ $booking->offer->city->name ?? '—' }}</span>
                        </div>

                        <div class="py-recap-row">
                            <span class="py-recap-lbl">
                                <i class="fas fa-calendar" aria-hidden="true"></i> Date
                            </span>
                            <span class="py-recap-val">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('D MMM YYYY') }}
                            </span>
                        </div>

                        @if($booking->booking_time)
                            <div class="py-recap-row">
                                <span class="py-recap-lbl">
                                    <i class="fas fa-clock" aria-hidden="true"></i> Heure
                                </span>
                                <span class="py-recap-val">{{ $booking->booking_time }}</span>
                            </div>
                        @endif

                        <div class="py-recap-row">
                            <span class="py-recap-lbl">
                                <i class="fas fa-users" aria-hidden="true"></i> Participants
                            </span>
                            <span class="py-recap-val">
                                {{ $booking->participants }}
                                personne{{ $booking->participants > 1 ? 's' : '' }}
                            </span>
                        </div>

                        @if($booking->tier && $tierLabel)
                            <div class="py-recap-row">
                                <span class="py-recap-lbl">
                                    <i class="fas fa-tag" aria-hidden="true"></i> Formule
                                </span>
                                <span class="py-recap-val">{{ $tierLabel }}</span>
                            </div>
                        @endif

                        <div class="py-recap-divider" aria-hidden="true"></div>

                        <div class="py-recap-total">
                            <span class="py-recap-total-lbl">Total à régler</span>
                            <div class="py-recap-total-amount">
                                <span class="py-recap-total-num">
                                    {{ number_format($booking->total_price, 0, ',', ' ') }}
                                </span>
                                <span class="py-recap-total-cur">FCFA</span>
                            </div>
                        </div>

                    </div>
                </div>

                <a href="{{ $bookingUrl }}" class="py-back-link">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Voir ma réservation
                </a>

                <a href="https://wa.me/{{ config('discovtrip.whatsapp_phone_raw', '22901000000') }}?text={{ urlencode('Bonjour, j\'ai une question sur le paiement de ma réservation ' . $booking->reference) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="py-wa">
                    <div class="py-wa-icon" aria-hidden="true">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="py-wa-title">Une question ?</div>
                        <div class="py-wa-sub">WhatsApp · Réponse rapide</div>
                    </div>
                    <i class="fas fa-arrow-right py-wa-arrow" aria-hidden="true"></i>
                </a>

            </aside>
        </div>
    </div>

</div>

{{-- ══ KKIAPAY SDK ══════════════════════════════════════════ --}}
@if($kkiapayEnabled)
@push('scripts')
<script src="https://cdn.kkiapay.me/k.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /**
     * CORRECTION : fonction renommée "launchKkiapay" pour éviter le conflit
     * avec la fonction native "openKkiapayWidget" fournie par le SDK KKiaPay.
     * Le bouton appelle launchKkiapay(), qui appelle openKkiapayWidget() du SDK.
     */
    window.launchKkiapay = function () {
        openKkiapayWidget({
            amount:   {{ (int) $booking->total_price }},
            api_key:  '{{ config('services.kkiapay.public_key') }}',
            sandbox:  {{ config('services.kkiapay.sandbox') ? 'true' : 'false' }},
            email:    '{{ $booking->guest_email ?? optional($booking->user)->email ?? '' }}',
            phone:    '{{ $booking->guest_phone ?? '' }}',
            name:     '{{ addslashes($clientName) }}',
            callback: '{{ $kkiapayCallback }}',
        });
    };

    addSuccessListener(function (response) {
        console.log('KKiaPay success event:', response);
    });

    addFailedListener(function (response) {
        console.error('KKiaPay failed event:', response);
        const alertEl = document.createElement('div');
        alertEl.className = 'py-alert py-alert--err';
        alertEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Paiement échoué. Veuillez réessayer ou choisir un autre moyen de paiement.';
        document.querySelector('.py-main').prepend(alertEl);
        alertEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

});
</script>
@endpush
@endif
@endsection