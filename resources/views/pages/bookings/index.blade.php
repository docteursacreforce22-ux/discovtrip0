@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/bookings/index.css')
@endpush

@section('title', 'Mes réservations — DiscovTrip')

@section('content')

<div class="bki-page">

    {{-- ── HEADER ──────────────────────────────────── --}}
    <div class="bki-header">
        <div class="bki-header-inner">
            <div>
                <nav class="bki-breadcrumb">
                    <a href="{{ route('account.dashboard') }}">Mon compte</a>
                    <span>›</span>
                    <span>Mes réservations</span>
                </nav>
                <h1 class="bki-title">Mes réservations</h1>
                <p class="bki-sub">
                    {{ $bookings->total() }} réservation{{ $bookings->total() > 1 ? 's' : '' }} au total
                </p>
            </div>
            <a href="{{ route('offers.index') }}" class="bki-new-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Nouvelle expérience
            </a>
        </div>
    </div>

    <div class="bki-body">

        @if($bookings->count() > 0)

            {{-- ── FILTRES ──────────────────────────── --}}
            <div class="bki-filters">
                @foreach(['all' => 'Toutes', 'confirmed' => 'Confirmées', 'pending' => 'En attente', 'completed' => 'Terminées', 'cancelled_by_user' => 'Annulées'] as $val => $label)
                    <a href="{{ route('bookings.index', $val !== 'all' ? ['status' => $val] : []) }}"
                       class="bki-filter {{ request('status', 'all') === $val ? 'bki-filter--active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- ── LISTE ────────────────────────────── --}}
            <div class="bki-list">
                @foreach($bookings as $booking)
                    @php
                        $participants = $booking->participants ?? 1;
                        $isPaid   = $booking->is_paid || ($booking->payment_status ?? '') === 'paid';
                        $isOnSite = ($booking->payment_method ?? 'on_site') === 'on_site';

                        // Classe CSS — NOTE : --confirmed etc. → classe bki-status--confirmed
                        $statusMod = match($booking->status) {
                            'confirmed'            => '--confirmed',
                            'cancelled_by_user',
                            'cancelled_by_partner' => '--cancelled',
                            'completed'            => '--completed',
                            default                => '--pending',
                        };
                        $statusLabel = match($booking->status) {
                            'confirmed'            => 'Confirmée',
                            'cancelled_by_user',
                            'cancelled_by_partner' => 'Annulée',
                            'completed'            => 'Terminée',
                            default                => 'En attente',
                        };
                        $emailParam = $booking->guest_email ? '?email=' . urlencode($booking->guest_email) : '';
                        $showUrl    = route('bookings.show', $booking->reference) . $emailParam;
                        $pdfUrl     = route('bookings.pdf',  $booking->reference) . $emailParam;
                    @endphp

                    <div class="bki-item">

                        {{-- Image --}}
                        <a href="{{ $showUrl }}" class="bki-item-img-wrap">
                            @if($booking->offer->cover_image)
                                <img src="{{ Storage::url($booking->offer->cover_image) }}"
                                     alt="{{ $booking->offer->title }}"
                                     class="bki-item-img">
                            @else
                                <div class="bki-item-img-ph">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                </div>
                            @endif
                        </a>

                        {{-- Corps --}}
                        <div class="bki-item-body">
                            <div class="bki-item-top">
                                <div style="min-width:0;">
                                    <div class="bki-item-city">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $booking->offer->city->name }}
                                    </div>
                                    <a href="{{ $showUrl }}" class="bki-item-title">{{ $booking->offer->title }}</a>
                                    @if($booking->tier)
                                        <div class="bki-item-tier">
                                            {{ $booking->tier->emoji ?? '' }} {{ $booking->tier->label }}
                                        </div>
                                    @endif
                                </div>
                                {{-- bki-status bki-status--confirmed etc. --}}
                                <span class="bki-status bki-status{{ $statusMod }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="bki-item-meta">
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                </span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    {{ $participants }} pers.
                                </span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    {{ number_format($booking->total_price, 0, ',', ' ') }} FCFA
                                </span>
                                <span class="bki-item-ref">
                                    # {{ $booking->reference }}
                                </span>
                            </div>

                            @if(!$isOnSite && !$isPaid && in_array($booking->status, ['pending','confirmed']))
                                <div class="bki-pay-alert">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    Paiement en attente —
                                    <a href="{{ route('payment.show', $booking->reference) }}">Payer maintenant</a>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="bki-item-actions">
                            <a href="{{ $showUrl }}" class="bki-btn bki-btn--primary">
                                Voir
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ $pdfUrl }}" target="_blank" class="bki-btn bki-btn--ghost" title="Bon PDF">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                                PDF
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- ── PAGINATION ───────────────────────── --}}
            <div class="bki-pagination">
                {{ $bookings->withQueryString()->links() }}
            </div>

        @else

            {{-- ── VIDE ─────────────────────────────── --}}
            <div class="bki-empty">
                <div class="bki-empty-icon">🗺️</div>
                <h3>Aucune réservation pour l'instant</h3>
                <p>Votre prochaine aventure au Bénin n'attend que vous !</p>
                <a href="{{ route('offers.index') }}" class="bki-new-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Découvrir les expériences
                </a>
            </div>

        @endif

    </div>{{-- fin bki-body --}}
</div>

@endsection