@extends('layouts.app')
@section('title', 'Mes favoris · DiscovTrip')

@push('styles')
    @vite(['resources/css/pages/account/layout.css', 'resources/css/pages/account/wishlist.css'])
@endpush

@section('content')
<div class="acl-root">
    @include('account._sidebar')

    <main class="acl-main" id="acl-main">

        <div class="acl-page-header">
            <div>
                <h1 class="acl-page-title">Mes favoris</h1>
                <p class="acl-page-sub">
                    {{ $wishlistItems->total() }}
                    expérience{{ $wishlistItems->total() > 1 ? 's' : '' }}
                    sauvegardée{{ $wishlistItems->total() > 1 ? 's' : '' }}
                </p>
            </div>
            <a href="{{ route('offers.index') }}" class="acl-btn acl-btn--outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Explorer les offres
            </a>
        </div>

        @if(session('success'))
        <div class="acl-alert acl-alert--ok" role="alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @forelse($wishlistItems as $item)
        @php
            $offer    = $item->offer;
            $minPrice = $offer->activeTiers->min('price') ?? $offer->effective_price ?? $offer->base_price;
            $hasPromo = $offer->is_promo;
        @endphp
        <article class="wl-card" data-offer-id="{{ $offer->id }}" aria-label="{{ $offer->title }}">

            <a href="{{ route('offers.show', $offer->slug) }}"
               class="wl-thumb"
               tabindex="-1" aria-hidden="true"
               {{-- CORRECTION : cover_image (pas cover_photo) --}}
               @if($offer->cover_image)
                   style="background-image:url('{{ asset('storage/' . $offer->cover_image) }}')"
               @endif>
                @if($hasPromo)
                    <span class="wl-promo" aria-label="En promotion">Promo −{{ $offer->promo_discount }}%</span>
                @endif
            </a>

            <div class="wl-body">
                <div class="wl-meta">
                    @if($offer->city)
                    <span class="wl-chip">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $offer->city->name }}
                    </span>
                    @endif
                    @if($offer->duration_formatted)
                    <span class="wl-chip">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $offer->duration_formatted }}
                    </span>
                    @endif
                </div>

                <h3 class="wl-title">
                    <a href="{{ route('offers.show', $offer->slug) }}">{{ $offer->title }}</a>
                </h3>

                @if($offer->short_description)
                <p class="wl-desc">{{ Str::limit($offer->short_description, 110) }}</p>
                @endif

                <div class="wl-footer">
                    <div class="wl-price">
                        @if($hasPromo)
                            <span class="wl-price-old">{{ number_format($offer->base_price, 0, ',', ' ') }}</span>
                            <span class="wl-price-promo">{{ number_format($offer->promotional_price, 0, ',', ' ') }} FCFA</span>
                        @else
                            À partir de <strong>{{ number_format($minPrice, 0, ',', ' ') }} FCFA</strong>
                        @endif
                    </div>
                    <div class="wl-actions">
                        <a href="{{ route('offers.show', $offer->slug) }}" class="acl-btn acl-btn--primary wl-btn">
                            Réserver
                        </a>
                        <button class="wl-remove js-wl-remove"
                                data-offer-id="{{ $offer->id }}"
                                type="button"
                                aria-label="Retirer {{ $offer->title }} des favoris">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                        </button>
                    </div>
                </div>
            </div>

        </article>
        @empty
        <div class="acl-empty" role="status">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--tx-muted)" stroke-width="1.2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <p>Aucune expérience sauvegardée</p>
            <p style="font-size:.8rem">Cliquez sur ♡ sur n'importe quelle offre pour l'ajouter ici.</p>
            <a href="{{ route('offers.index') }}" class="acl-empty-cta">Explorer les expériences</a>
        </div>
        @endforelse

        @if($wishlistItems->hasPages())
        <div class="wl-pagination">{{ $wishlistItems->links('pagination::simple-bootstrap-4') }}</div>
        @endif

    </main>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-wl-remove').forEach(function (btn) {
    btn.addEventListener('click', async function () {
        var offerId = this.dataset.offerId;
        var card    = this.closest('.wl-card');
        try {
            var res = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':        'application/json',
                },
                body: JSON.stringify({ offer_id: offerId }),
                credentials: 'same-origin',
            });
            if (res.ok) {
                card.style.transition = 'opacity .3s, transform .3s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(.97)';
                setTimeout(function () { card.remove(); }, 320);
            }
        } catch (e) {
            console.error('[Wishlist remove]', e);
        }
    });
});
</script>
@endpush