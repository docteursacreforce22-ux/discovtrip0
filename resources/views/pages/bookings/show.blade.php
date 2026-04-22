@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/bookings/show.css')
@endpush

@section('title', 'Réservation ' . $booking->reference . ' — DiscovTrip')

@section('content')

@php
    $participants = $booking->participants ?? 1;
    $isPaid       = $booking->is_paid || ($booking->payment_status ?? '') === 'paid';
    $isOnSite     = ($booking->payment_method ?? 'on_site') === 'on_site';
    $canCancel    = in_array($booking->status, ['pending','confirmed'])
                 && \Carbon\Carbon::parse($booking->booking_date)->isFuture()
                 && \Carbon\Carbon::parse($booking->booking_date)->diffInHours(now()) >= 48;

    $pdfUrl = auth()->check()
        ? route('bookings.pdf', $booking->reference)
        : \Illuminate\Support\Facades\URL::signedRoute('bookings.pdf', ['reference' => $booking->reference]);

    // Modificateur CSS — bks-status bks-pill--confirmed etc.
    $pillMod = match($booking->status) {
        'confirmed'                          => '--confirmed',
        'cancelled_by_user',
        'cancelled_by_partner'               => '--cancelled',
        'completed'                          => '--completed',
        default                              => '--pending',
    };
    $statusLabel = match($booking->status) {
        'confirmed'                          => 'Réservation confirmée',
        'cancelled_by_user',
        'cancelled_by_partner'               => 'Réservation annulée',
        'completed'                          => 'Expérience terminée',
        default                              => 'En attente de confirmation',
    };
    $heroTitle = match($booking->status) {
        'confirmed'                          => '🎉 Réservation confirmée !',
        'cancelled_by_user',
        'cancelled_by_partner'               => '✗ Réservation annulée',
        'completed'                          => '✓ Expérience terminée',
        default                              => '⏳ Demande envoyée',
    };
    $heroSub = match($booking->status) {
        'confirmed'        => 'Préparez-vous pour une expérience inoubliable au Bénin. 🇧🇯',
        'cancelled_by_user',
        'cancelled_by_partner' => 'Cette réservation a été annulée.',
        'completed'        => 'Merci d\'avoir voyagé avec DiscovTrip. À bientôt !',
        default            => 'Votre guide confirmera sous 24h. Un email vous a été envoyé.',
    };

    $waPhone = config('discovtrip.whatsapp_phone_raw', '22901910943');
    $waMsg   = urlencode('Bonjour DiscovTrip, question sur ma réservation ' . $booking->reference);
@endphp

<div class="bks-page">

    {{-- ── HERO ──────────────────────────────────── --}}
    <div class="bks-hero">

        @if(session('success'))
            <div class="bks-confetti" aria-hidden="true">
                @php
                for ($i = 0; $i < 20; $i++) {
                    echo '<div class="bks-confetti-piece" style="left:' . ($i * 5) . '%;animation-delay:' . ($i * 0.07) . 's;animation-duration:' . (2 + ($i % 4) * 0.25) . 's;"></div>';
                }
                @endphp
            </div>
        @endif

        <div class="bks-hero-inner">
            {{-- bks-status-pill bks-pill--confirmed etc. --}}
            <div class="bks-status-pill bks-pill{{ $pillMod }}">
                {{ $statusLabel }}
            </div>
            <h1 class="bks-hero-title">{{ $heroTitle }}</h1>
            <p class="bks-hero-sub">{{ $heroSub }}</p>
            <div class="bks-ref-chip">
                # {{ $booking->reference }}
                <button type="button" class="bks-copy-btn"
                        onclick="bksCopyRef('{{ $booking->reference }}')"
                        title="Copier">
                    <svg width="13" height="13" id="bks-copy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
            </div>
            @if($booking->guest_email)
                <div class="bks-guest-alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Confirmation envoyée à
                    <strong style="margin:0 .2rem;">{{ $booking->guest_email }}</strong>
                    — conservez cet email.
                </div>
            @endif
        </div>
    </div>

    {{-- ── LAYOUT ───────────────────────────────── --}}
    <div class="bks-body">

        {{-- ── COLONNE GAUCHE ───────────────────── --}}
        <div>

            {{-- L'expérience --}}
            <div class="bks-card">
                <div class="bks-card-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    L'expérience
                </div>
                <div class="bks-offer-row">
                    @if($booking->offer->cover_image)
                        <img src="{{ Storage::url($booking->offer->cover_image) }}"
                             alt="{{ $booking->offer->title }}"
                             class="bks-offer-img">
                    @else
                        <div class="bks-offer-img-ph">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        </div>
                    @endif
                    <div>
                        <div class="bks-offer-city">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $booking->offer->city->name }}, Bénin 🇧🇯
                        </div>
                        <div class="bks-offer-name">{{ $booking->offer->title }}</div>
                        @if($booking->tier)
                            <div class="bks-offer-tier">
                                {{ $booking->tier->emoji ?? '' }} {{ $booking->tier->label }}
                            </div>
                        @endif
                        <a href="{{ route('offers.show', $booking->offer->slug) }}" class="bks-offer-link">
                            Voir l'offre
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Détails --}}
            <div class="bks-card">
                <div class="bks-card-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Détails de la réservation
                </div>
                <div class="bks-details-grid">

                    {{-- Date --}}
                    <div class="bks-detail">
                        <div class="bks-detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div>
                            <div class="bks-detail-lbl">Date</div>
                            <div class="bks-detail-val">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </div>
                            @if($booking->booking_time)
                                <div class="bks-detail-sub">⏰ {{ $booking->booking_time }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Participants --}}
                    <div class="bks-detail">
                        <div class="bks-detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div>
                            <div class="bks-detail-lbl">Participants</div>
                            <div class="bks-detail-val">
                                {{ $participants }} personne{{ $participants > 1 ? 's' : '' }}
                            </div>
                        </div>
                    </div>

                    {{-- Paiement --}}
                    <div class="bks-detail">
                        <div class="bks-detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <div>
                            <div class="bks-detail-lbl">Paiement</div>
                            <div class="bks-detail-val">
                                @if($isOnSite) Sur place le jour J
                                @elseif($booking->payment_method === 'fedapay') Mobile Money (FedaPay)
                                @else Carte bancaire (Stripe)
                                @endif
                                @if($isPaid)
                                    <span class="bks-chip bks-chip--paid">✓ Payé</span>
                                @elseif(!$isOnSite)
                                    <span class="bks-chip bks-chip--pending">En attente</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Référence --}}
                    <div class="bks-detail">
                        <div class="bks-detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        </div>
                        <div>
                            <div class="bks-detail-lbl">Référence</div>
                            <div class="bks-detail-val" style="font-family:monospace;">
                                {{ $booking->reference }}
                            </div>
                        </div>
                    </div>

                    {{-- Client (guest uniquement) --}}
                    @if($booking->guest_email)
                        <div class="bks-detail" style="grid-column: 1 / -1;">
                            <div class="bks-detail-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="bks-detail-lbl">Client</div>
                                <div class="bks-detail-val">
                                    {{ trim(($booking->guest_first_name ?? '') . ' ' . ($booking->guest_last_name ?? '')) }}
                                </div>
                                <div class="bks-detail-sub">{{ $booking->guest_email }}</div>
                                @if($booking->guest_phone)
                                    <div class="bks-detail-sub">{{ $booking->guest_phone }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

                @if($booking->notes)
                    <div class="bks-notes">
                        <div class="bks-notes-lbl">Message</div>
                        <p>{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Paiement requis --}}
            @if(!$isOnSite && !$isPaid && in_array($booking->status, ['pending','confirmed']))
                <div class="bks-card bks-card--warn">
                    <div class="bks-card-label" style="color:#92400e;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Paiement requis
                    </div>
                    <p style="font-family:var(--font-body);font-size:.85rem;color:var(--tx-mid);margin-bottom:1rem;line-height:1.65;">
                        Finalisez votre paiement pour confirmer définitivement votre place.
                    </p>
                    <a href="{{ route('payment.show', $booking->reference) }}" class="bks-pay-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Payer maintenant
                    </a>
                </div>
            @endif

            {{-- Actions — annulation uniquement. PDF et WhatsApp sont dans la sidebar. --}}
            @auth
                    @if($canCancel)
                        <form action="{{ route('bookings.cancel', $booking->reference) }}" method="POST"
                              onsubmit="return confirm('Annuler cette réservation ? Cette action est irréversible.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="bks-action bks-action--cancel">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                Annuler la réservation
                            </button>
                        </form>
                    @endif
                @endauth

        </div>{{-- fin colonne gauche --}}

        {{-- ── SIDEBAR ──────────────────────────── --}}
        <aside>

            {{-- Prix --}}
            <div class="bks-card">
                <div class="bks-card-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Récapitulatif
                </div>
                <div class="bks-price-rows">
                    <div class="bks-price-row">
                        <span>Prix unitaire</span>
                        <span>{{ number_format($booking->total_price / ($participants ?: 1), 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="bks-price-row">
                        <span>Participants</span>
                        <span>× {{ $participants }}</span>
                    </div>
                    @if($booking->tier)
                        <div class="bks-price-row">
                            <span>Formule</span>
                            <span>{{ $booking->tier->emoji ?? '' }} {{ $booking->tier->label }}</span>
                        </div>
                    @endif
                    <div class="bks-price-row bks-price-row--total">
                        <span>Total</span>
                        <span>{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <div class="bks-price-eur">
                    ≈ {{ number_format($booking->total_price / 655.957, 0, ',', ' ') }} €
                </div>
            </div>

            {{-- PDF --}}
            <a href="{{ $pdfUrl }}" target="_blank" class="bks-pdf-card">
                <div class="bks-pdf-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <div class="bks-pdf-title">Bon de réservation</div>
                    <div class="bks-pdf-sub">Télécharger · Imprimer · PDF</div>
                </div>
                <div class="bks-pdf-dl">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- À savoir --}}
            <div class="bks-card">
                <div class="bks-card-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    À savoir
                </div>
                <div class="bks-tips">
                    <div class="bks-tip">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Point de rendez-vous communiqué par votre guide 24h avant.
                    </div>
                    <div class="bks-tip">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/></svg>
                        Annulation gratuite jusqu'à 48h avant la date.
                    </div>
                    <div class="bks-tip">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                        Présentez votre bon (numérique ou imprimé) le jour J.
                    </div>
                </div>
            </div>

            {{-- Aide --}}
            <div class="bks-card">
                <div class="bks-card-label">Besoin d'aide ?</div>
                <p style="font-family:var(--font-body);font-size:.8rem;color:var(--tx-soft);margin-bottom:.875rem;line-height:1.6;">
                    Notre équipe locale vous répond rapidement.
                </p>
                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}"
                   target="_blank" rel="noopener" class="bks-wa-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            </div>

            <a href="{{ route('offers.index') }}" class="bks-explore-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Explorer d'autres expériences
            </a>

        </aside>
    </div>{{-- fin bks-body --}}
</div>

@endsection

@push('scripts')
<script>
function bksCopyRef(ref) {
    navigator.clipboard.writeText(ref).then(function () {
        var icon = document.getElementById('bks-copy-icon');
        if (!icon) return;
        icon.innerHTML = '<polyline points="20 6 9 17 4 12" stroke="var(--f-300)" stroke-width="2.5" fill="none"/>';
        setTimeout(function () {
            icon.innerHTML = '<rect x="9" y="9" width="13" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" fill="none" stroke="currentColor" stroke-width="2"/>';
        }, 2000);
    });
}
</script>
@endpush