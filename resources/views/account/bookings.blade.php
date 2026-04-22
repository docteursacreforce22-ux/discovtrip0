@extends('layouts.app')
@section('title', 'Mes réservations · DiscovTrip')

@push('styles')
    @vite(['resources/css/pages/account/layout.css', 'resources/css/pages/account/bookings.css'])
@endpush

@section('content')
<div class="acl-root">
    @include('account._sidebar')

    <main class="acl-main" id="acl-main">

        {{-- Header --}}
        <div class="acl-page-header">
            <div>
                <h1 class="acl-page-title">Mes réservations</h1>
                <p class="acl-page-sub">
                    {{ $bookings->total() }} réservation{{ $bookings->total() > 1 ? 's' : '' }} au total
                </p>
            </div>
            <a href="{{ route('offers.index') }}" class="acl-btn acl-btn--primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Nouvelle réservation
            </a>
        </div>

        {{-- Alertes session --}}
        @if(session('success'))
        <div class="acl-alert acl-alert--ok" role="alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="acl-alert acl-alert--err" role="alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Filtres --}}
        <nav class="bk-filters" aria-label="Filtrer les réservations">
            @foreach([
                ''                  => 'Toutes',
                'confirmed'         => 'Confirmées',
                'pending'           => 'En attente',
                'completed'         => 'Terminées',
                'cancelled_by_user' => 'Annulées',
            ] as $val => $lbl)
            <a href="{{ route('account.bookings', $val ? ['status' => $val] : []) }}"
               class="bk-filter {{ request('status', '') === $val ? 'bk-filter--on' : '' }}"
               @if(request('status','') === $val) aria-current="true" @endif>
                {{ $lbl }}
                @if($val === '' && $bookings->total() > 0)
                    <span class="bk-filter-n">{{ $bookings->total() }}</span>
                @endif
            </a>
            @endforeach
        </nav>

        {{-- Liste --}}
        @forelse($bookings as $booking)
        @php
            $offer  = $booking->offer;
            // CORRECTION : cover_image (pas cover_photo)
            $img    = $offer?->cover_image ? asset('storage/' . $offer->cover_image) : null;
            $isPast = \Carbon\Carbon::parse($booking->booking_date)->isPast();
            // CORRECTION : utiliser canCancel() du modèle Booking
            $canCancel     = $booking->canCancel();
            $statusMod     = $booking->status_modifier;
            $statusLabel   = $booking->status_label;
        @endphp
        <article class="bk-row {{ $isPast && !in_array($booking->status, ['confirmed','pending']) ? 'bk-row--dim' : '' }}"
                 aria-label="Réservation {{ $booking->reference }}">

            {{-- Vignette --}}
            <div class="bk-thumb"
                 @if($img) style="background-image:url('{{ $img }}')" @endif
                 role="img"
                 aria-label="{{ $offer?->title ?? 'Expérience' }}">
                @if(!$img)
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--tx-muted)" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                @endif
            </div>

            {{-- Infos --}}
            <div class="bk-info">
                <div class="bk-info-top">
                    <code class="bk-ref">{{ $booking->reference }}</code>
                    <span class="acl-pill acl-pill--{{ $statusMod }}">{{ $statusLabel }}</span>
                </div>
                <div class="bk-name">{{ $offer?->title ?? '—' }}</div>
                <div class="bk-meta">
                    @if($offer?->city)
                    <span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $offer->city->name }}
                    </span>
                    @endif
                    <span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('D MMMM YYYY') }}
                        @if($booking->booking_time) · {{ $booking->booking_time }}@endif
                    </span>
                    <span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        {{ $booking->total_participants }} participant{{ $booking->total_participants > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            {{-- Prix --}}
            <div class="bk-price-col">
                <div class="bk-price">{{ number_format($booking->total_price, 0, ',', ' ') }}</div>
                <div class="bk-currency">FCFA</div>
            </div>

            {{-- Actions --}}
            <div class="bk-actions">
                <a href="{{ route('bookings.show', $booking->reference) }}" class="bk-btn bk-btn--view">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Détail
                </a>

                @if(in_array($booking->status, ['confirmed', 'completed']))
                <a href="{{ route('bookings.pdf', $booking->reference) }}"
                   target="_blank"
                   class="bk-btn bk-btn--pdf"
                   aria-label="Télécharger le bon PDF">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    PDF
                </a>
                @endif

                @if($canCancel)
                <button type="button"
                        class="bk-btn bk-btn--cancel"
                        onclick="openCancelModal('{{ $booking->reference }}','{{ addslashes($offer?->title ?? '') }}','{{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('D MMM YYYY') }}')"
                        aria-label="Annuler la réservation {{ $booking->reference }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Annuler
                </button>
                @endif
            </div>

        </article>
        @empty
        <div class="acl-empty" role="status">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--tx-muted)" stroke-width="1.2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p>Aucune réservation @if(request('status'))pour ce filtre@endif</p>
            <a href="{{ route('offers.index') }}" class="acl-empty-cta">Explorer les expériences</a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="bk-pagination">{{ $bookings->withQueryString()->links('pagination::simple-bootstrap-4') }}</div>
        @endif

    </main>
</div>

{{-- Modal annulation --}}
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
                <li>Plus de 48h avant — remboursement intégral</li>
                <li>Entre 24h et 48h — remboursement 50%</li>
                <li>Moins de 24h — aucun remboursement</li>
            </ul>
        </div>
        <p class="acl-modal-text">Cette action est <strong>irréversible</strong>. Confirmez-vous ?</p>
        <div class="acl-modal-btns">
            <button class="acl-modal-btn acl-modal-btn--keep" onclick="closeCancelModal()">Non, garder</button>
            <form id="modal-form" method="POST" action="" style="flex:1">
                @csrf @method('PATCH')
                <button type="submit" class="acl-modal-btn acl-modal-btn--confirm">Oui, annuler</button>
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
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeCancelModal(); });
</script>
@endpush