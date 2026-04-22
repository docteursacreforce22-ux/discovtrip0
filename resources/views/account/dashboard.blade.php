@extends('layouts.app')
@section('title', 'Mon espace · DiscovTrip')

@push('styles')
    @vite(['resources/css/pages/account/layout.css', 'resources/css/pages/account/dashboard.css'])
@endpush

@section('content')
@php
    $firstName = $user->first_name ?? explode(' ', $user->name ?? '')[0] ?? 'Voyageur';
    $hour      = now()->hour;
    $greeting  = match(true) {
        $hour < 6  => 'Bonne nuit',
        $hour < 12 => 'Bonjour',
        $hour < 18 => 'Bon après-midi',
        default    => 'Bonsoir',
    };
    $nextTrip = $upcomingBookings->first();
@endphp

<div class="acl-root">

    @include('account._sidebar')

    <main class="acl-main" id="acl-main">

        {{-- ── HEADER ──────────────────────────────────── --}}
        <div class="adb-header">
            <div class="adb-header-left">
                <div class="adb-greeting">{{ $greeting }},</div>
                <h1 class="adb-name">{{ $firstName }}</h1>
                @if($nextTrip)
                    @php
                        $daysLeft = max(0, \Carbon\Carbon::parse($nextTrip->booking_date)->diffInDays(now(), false) * -1);
                    @endphp
                    <div class="adb-next-trip">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        Prochain voyage dans <strong>{{ $daysLeft }}j</strong> · {{ Str::limit($nextTrip->offer?->title ?? '', 40) }}
                    </div>
                @endif
            </div>
            <div class="adb-header-right">
                <a href="{{ route('offers.index') }}" class="acl-btn acl-btn--primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Explorer
                </a>
            </div>
        </div>

        {{-- ── STATS ────────────────────────────────────── --}}
        <div class="adb-stats" role="list" aria-label="Statistiques de votre compte">
            @foreach([
                [$stats['total'],     'Réservations', 'gold',  'M3 4h18v18H3V4zm5-2v2m8-2v2M3 10h18'],
                [$stats['upcoming'],  'À venir',      'teal',  'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20m0 4v4l3 3'],
                [$stats['completed'], 'Terminées',    'green', 'M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4 12 14.01l-3-3'],
                [$stats['wishlist'],  'Favoris',      'rose',  'M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z'],
            ] as [$val, $lbl, $col, $path])
            <div class="adb-stat adb-stat--{{ $col }}" role="listitem">
                <div class="adb-stat-top">
                    <div class="adb-stat-icon-wrap" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $path }}"/></svg>
                    </div>
                </div>
                <div class="adb-stat-val">{{ $val }}</div>
                <div class="adb-stat-lbl">{{ $lbl }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── PROCHAINS VOYAGES ────────────────────────── --}}
        <section class="acl-card" aria-labelledby="adb-trips-title">
            <div class="adb-section-head">
                <h2 class="adb-section-title" id="adb-trips-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    Prochains voyages
                </h2>
                <a href="{{ route('account.bookings') }}" class="adb-section-more">
                    Tout voir
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            @if($upcomingBookings->isNotEmpty())
                <div class="adb-trips">
                    @foreach($upcomingBookings->take(3) as $booking)
                    @php
                        $offer = $booking->offer;
                        // CORRECTION : cover_image (pas cover_photo)
                        $img   = $offer?->cover_image ? asset('storage/' . $offer->cover_image) : null;
                        $days  = max(0, \Carbon\Carbon::parse($booking->booking_date)->diffInDays(now(), false) * -1);
                        // CORRECTION : utiliser canCancel() du modèle Booking
                        $canCancel = $booking->canCancel();
                        // CORRECTION : utiliser status_modifier et status_label du modèle
                        $statusMod   = $booking->status_modifier;
                        $statusLabel = $booking->status_label;
                    @endphp
                    <div class="adb-trip">
                        <div class="adb-trip-img"
                             @if($img) style="background-image:url('{{ $img }}')" @endif
                             role="img"
                             aria-label="{{ $offer?->title ?? 'Expérience' }}">
                            <div class="adb-trip-img-overlay">
                                <span class="acl-pill acl-pill--{{ $statusMod }}">{{ $statusLabel }}</span>
                                @if($days <= 14 && in_array($booking->status, ['confirmed', 'pending']))
                                    <span class="adb-trip-days">
                                        @if($days === 0) Auj.
                                        @elseif($days === 1) Demain
                                        @else {{ $days }}j
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="adb-trip-body">
                            <div class="adb-trip-ref">{{ $booking->reference }}</div>
                            <div class="adb-trip-name">{{ $offer?->title ?? 'Expérience' }}</div>
                            <div class="adb-trip-meta">
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $offer?->city?->name ?? 'Bénin' }}
                                </span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('D MMM') }}
                                </span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    {{ $booking->total_participants }} pers.
                                </span>
                            </div>
                            <div class="adb-trip-price">
                                {{ number_format($booking->total_price, 0, ',', ' ') }}
                                <span>FCFA</span>
                            </div>
                        </div>
                        <div class="adb-trip-actions">
                            <a href="{{ route('bookings.show', $booking->reference) }}"
                               class="adb-trip-btn adb-trip-btn--detail">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Voir
                            </a>
                            @if($canCancel)
                                <button type="button"
                                        class="adb-trip-btn adb-trip-btn--cancel"
                                        onclick="openCancelModal('{{ $booking->reference }}','{{ addslashes($offer?->title ?? '') }}','{{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('D MMM YYYY') }}')"
                                        aria-label="Annuler la réservation {{ $booking->reference }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    Annuler
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="acl-empty" style="border:none;padding:36px 0 8px">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--tx-muted)" stroke-width="1.2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <p>Aucun voyage à venir</p>
                    <a href="{{ route('offers.index') }}" class="acl-empty-cta">Réserver une expérience</a>
                </div>
            @endif
        </section>

        {{-- ── GRID BAS ─────────────────────────────────── --}}
        <div class="adb-grid-2">

            {{-- Favoris --}}
            <section class="acl-card" aria-labelledby="adb-wish-title">
                <div class="adb-section-head">
                    <h2 class="adb-section-title" id="adb-wish-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        Favoris
                    </h2>
                    <a href="{{ route('account.wishlist') }}" class="adb-section-more">Voir tout →</a>
                </div>
                @if($wishlistItems->isNotEmpty())
                    <div class="adb-wish">
                        @foreach($wishlistItems->take(4) as $item)
                        @php $wo = $item->offer; @endphp
                        <a href="{{ route('offers.show', $wo->slug) }}"
                           class="adb-wish-card"
                           aria-label="{{ $wo->title }}"
                           @if($wo->cover_image)
                               style="background-image:url('{{ asset('storage/' . $wo->cover_image) }}')"
                           @endif>
                            <div class="adb-wish-overlay">{{ Str::limit($wo->title, 26) }}</div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <p class="adb-empty-text">
                        Aucun favori —
                        <a href="{{ route('offers.index') }}" class="adb-link">Explorer →</a>
                    </p>
                @endif
            </section>

            {{-- Profil --}}
            <section class="acl-card" aria-labelledby="adb-profile-title">
                <div class="adb-section-head">
                    <h2 class="adb-section-title" id="adb-profile-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Mon profil
                    </h2>
                    <a href="{{ route('account.profile') }}" class="adb-section-more">Modifier →</a>
                </div>
                @php
                    $completion = collect([
                        $user->first_name, $user->last_name,
                        $user->phone, $user->nationality, $user->bio,
                    ])->filter()->count() * 20;
                @endphp
                <div class="adb-progress">
                    <div class="adb-progress-head">
                        <span>Profil complété</span>
                        <strong>{{ $completion }}%</strong>
                    </div>
                    <div class="adb-progress-bar" role="progressbar" aria-valuenow="{{ $completion }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="adb-progress-fill" style="width:{{ $completion }}%"></div>
                    </div>
                </div>
                <div class="adb-profile-rows">
                    @foreach([
                        ['Nom',   $user->name],
                        ['Email', $user->email],
                        ['Tél.',  $user->phone],
                        ['Pays',  $user->nationality],
                    ] as [$l, $v])
                    <div class="adb-profile-row">
                        <span>{{ $l }}</span>
                        <span class="{{ !$v ? 'adb-empty-val' : '' }}">{{ $v ?: '—' }}</span>
                    </div>
                    @endforeach
                </div>
            </section>

        </div>

    </main>
</div>

{{-- ══ MODAL ANNULATION ══ --}}
<div class="acl-modal-bg" id="modal-bg" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="acl-modal">
        <button class="acl-modal-x" onclick="closeCancelModal()" aria-label="Fermer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="acl-modal-icon" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h3 class="acl-modal-title" id="modal-title">Annuler la réservation</h3>
        <p class="acl-modal-offer" id="modal-offer">—</p>
        <p class="acl-modal-date"  id="modal-date">—</p>
        <div class="acl-modal-policy">
            <div class="acl-modal-policy-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Politique d'annulation
            </div>
            <ul class="acl-modal-policy-list">
                <li>Annulation &gt; 48h — remboursement intégral</li>
                <li>Entre 24h et 48h — remboursement 50%</li>
                <li>Annulation &lt; 24h — aucun remboursement</li>
            </ul>
        </div>
        <p class="acl-modal-text">Cette action est <strong>irréversible</strong>. Confirmez-vous l'annulation ?</p>
        <div class="acl-modal-btns">
            <button class="acl-modal-btn acl-modal-btn--keep" onclick="closeCancelModal()">Non, garder</button>
            <form id="modal-form" method="POST" action="" style="flex:1">
                @csrf @method('PATCH')
                <button type="submit" class="acl-modal-btn acl-modal-btn--confirm">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Oui, annuler
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCancelModal(ref, title, date) {
    document.getElementById('modal-offer').textContent = title;
    document.getElementById('modal-date').textContent  = date;
    document.getElementById('modal-form').action       = '/bookings/' + ref + '/cancel';
    document.getElementById('modal-bg').classList.add('acl-modal-bg--open');
    document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
    document.getElementById('modal-bg').classList.remove('acl-modal-bg--open');
    document.body.style.overflow = '';
}
document.getElementById('modal-bg').addEventListener('click', function (e) {
    if (e.target.id === 'modal-bg') closeCancelModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeCancelModal();
});
</script>
@endpush