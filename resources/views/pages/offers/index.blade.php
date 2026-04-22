@extends('layouts.app')

@section('title', 'Expériences au Bénin — DiscovTrip')
@section('description', 'Explorez toutes nos expériences authentiques au Bénin. Culture, gastronomie, nature, aventure. Guides locaux certifiés.')

@push('styles')
    @vite('resources/css/pages/offers/index.css')
@endpush

@section('content')

@php
$gradients = [
    'cultural'   => ['#D4E8C8','#8BBF6E'],
    'gastronomy' => ['#F5E6D3','#C8986A'],
    'nature'     => ['#C8E8F0','#6AB8CC'],
    'adventure'  => ['#F5EAD5','#C8A050'],
    'wellness'   => ['#ECD5E8','#B87AAA'],
    'urban'      => ['#D0DCF0','#7A9ACC'],
];
$emojis = [
    'cultural'   => '🕌',
    'gastronomy' => '🍽️',
    'nature'     => '🌊',
    'adventure'  => '🏔️',
    'wellness'   => '🧘',
    'urban'      => '🏙️',
];
$categories = [
    ''           => ['label' => 'Tout',      'icon' => '✦'],
    'cultural'   => ['label' => 'Culturel',  'icon' => '🕌'],
    'gastronomy' => ['label' => 'Gastro',    'icon' => '🍽️'],
    'nature'     => ['label' => 'Nature',    'icon' => '🌊'],
    'adventure'  => ['label' => 'Aventure',  'icon' => '🏔️'],
    'wellness'   => ['label' => 'Bien-être', 'icon' => '🧘'],
    'urban'      => ['label' => 'Urbain',    'icon' => '🏙️'],
];
$wishlistIds = Auth::check()
    ? Auth::user()->wishlists()->pluck('offer_id')->toArray()
    : [];

// Promos : requête séparée — indépendante de la pagination
// (injectées par OfferController::index via $promoOffers)
@endphp

{{-- ════ HERO ════ --}}
<section class="opl-hero" aria-label="Rechercher une expérience">
    {{-- Fond : image DB ou var(--f-900) + motif wax --}}
    <x-hero-bg setting-key="hero_offers" pattern-id="wp-opl" />

    <div class="opl-hero-inner">

        {{-- Texte --}}
        <div class="opl-hero-left">
            <div class="opl-count-badge" aria-live="polite">
                <span class="opl-count-badge-dot" aria-hidden="true"></span>
                <span id="opl-hero-counter" data-target="{{ $totalOffers }}">{{ $totalOffers }}</span>
                expérience{{ $totalOffers > 1 ? 's' : '' }} disponible{{ $totalOffers > 1 ? 's' : '' }}
                <span class="opl-count-badge-dot" aria-hidden="true"></span>
            </div>

            <h1 class="opl-hero-h1">
                Vivez le Bénin
                <em class="opl-hero-em">de l'intérieur</em>
            </h1>
            <p class="opl-hero-sub">
                Expériences conçues par des experts locaux certifiés.
                Chaque réservation soutient les communautés béninoises.
            </p>

            {{-- Recherche --}}
            <form action="{{ route('offers.index') }}" method="GET" class="opl-search-wrap">
                @foreach(request()->except('search','page') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <div class="opl-search-bar">
                    <i class="fas fa-search opl-search-icon" aria-hidden="true"></i>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Rechercher une expérience, une ville…"
                           class="opl-search-input"
                           autocomplete="off"
                           aria-label="Rechercher une expérience">
                    @if(request('search'))
                        <a href="{{ route('offers.index', request()->except('search','page')) }}"
                           class="opl-search-clear" title="Effacer la recherche" aria-label="Effacer">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </a>
                    @endif
                    <button type="submit" class="opl-search-btn" aria-label="Lancer la recherche">
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

            {{-- Quick filters — catégories uniquement, sans doublon promo --}}
            <nav class="opl-qf-bar" aria-label="Filtres rapides">
                @foreach($categories as $val => $cat)
                <a href="{{ route('offers.index', array_merge(request()->except('category','page'), $val ? ['category' => $val] : [])) }}"
                   class="opl-qf {{ request('category', '') === $val ? 'active' : '' }}"
                   @if(request('category','') === $val) aria-current="true" @endif>
                    <span aria-hidden="true">{{ $cat['icon'] }}</span>
                    {{ $cat['label'] }}
                </a>
                @endforeach
            </nav>
        </div>

        {{-- ── ASSEMBLAGE 2 PHOTOS ── --}}
        <div class="opl-hero-visual" aria-hidden="true">

            {{-- Photo principale --}}
            <div class="opl-hero-photo-main">
                @if(isset($heroImageMain) && $heroImageMain)
                    <img src="{{ mediaUrl($heroImageMain) }}"
                         alt="Expérience au Bénin"
                         loading="eager" width="480" height="640">
                @else
                    <div class="opl-hero-img-ph"
                         style="background:linear-gradient(160deg,#8BBF6E 0%,#2A8F5E 40%,#0D3822 100%);font-size:80px;">
                        🕌
                    </div>
                @endif
            </div>

            {{-- Photo secondaire --}}
            <div class="opl-hero-photo-secondary">
                @if(isset($heroImageSecondary) && $heroImageSecondary)
                    <img src="{{ mediaUrl($heroImageSecondary) }}"
                         alt="Découverte au Bénin"
                         loading="eager" width="280" height="200">
                @else
                    <div class="opl-hero-img-ph-sm"
                         style="background:linear-gradient(135deg,#C8E8F0,#6AB8CC);font-size:40px;">
                        🌊
                    </div>
                @endif
            </div>

            {{-- Badge flottant — note moyenne --}}
            <div class="opl-hero-badge">
                <div class="opl-hero-badge-icon" aria-hidden="true">⭐</div>
                <div>
                    <span class="opl-hero-badge-label">Note moyenne</span>
                    <strong class="opl-hero-badge-value">{{ number_format($avgRating ?? 4.8, 1) }} / 5</strong>
                </div>
            </div>

            {{-- Badge 2 — guides certifiés --}}
            <div class="opl-hero-badge-2">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                Guides certifiés
            </div>

        </div>
    </div>
</section>

{{-- ════ LIGNE ÉDITORIALE ════ --}}
<div class="opl-editorial" aria-hidden="true">
    <div class="opl-editorial-inner">
        <div class="opl-editorial-quote">
            Le Bénin ne se visite pas —
            <em class="opl-editorial-em">il se ressent.</em>
        </div>
        <div class="opl-editorial-sub">
            Chaque expérience est une rencontre. Chaque guide, une mémoire vivante.
        </div>
    </div>
</div>

{{-- ════ FILTRES ACTIFS ════ --}}
@if(request()->hasAny(['search','category','city','sort','instant','featured','min_price','max_price','promo']))
<div class="opl-active-bar" role="status" aria-label="Filtres actifs">
    <div class="opl-active-inner">
        <span class="opl-active-label">
            <i class="fas fa-filter" aria-hidden="true"></i> Filtres actifs
        </span>

        @if(request('search'))
            <span class="opl-chip">
                🔍 "{{ request('search') }}"
                <a href="{{ route('offers.index', request()->except('search','page')) }}" class="opl-chip-x" aria-label="Retirer ce filtre">×</a>
            </span>
        @endif
        @if(request('category'))
            <span class="opl-chip">
                {{ $categories[request('category')]['icon'] ?? '' }} {{ ucfirst(request('category')) }}
                <a href="{{ route('offers.index', request()->except('category','page')) }}" class="opl-chip-x" aria-label="Retirer">×</a>
            </span>
        @endif
        @if(request('city'))
            <span class="opl-chip">
                📍 {{ $cities->find(request('city'))?->name }}
                <a href="{{ route('offers.index', request()->except('city','page')) }}" class="opl-chip-x" aria-label="Retirer">×</a>
            </span>
        @endif
        @if(request('instant'))
            <span class="opl-chip">
                ⚡ Instantané
                <a href="{{ route('offers.index', request()->except('instant','page')) }}" class="opl-chip-x" aria-label="Retirer">×</a>
            </span>
        @endif
        @if(request('promo'))
            <span class="opl-chip" style="border-color:rgba(192,50,26,.22);background:rgba(192,50,26,.05);color:var(--b-500)">
                🔥 Promos
                <a href="{{ route('offers.index', request()->except('promo','page')) }}" class="opl-chip-x" aria-label="Retirer">×</a>
            </span>
        @endif

        <a href="{{ route('offers.index') }}" class="opl-active-clear">Tout effacer</a>
    </div>
</div>
@endif

{{-- ════ BODY ════ --}}
<div class="opl-body">

    {{-- ── SIDEBAR ── --}}
    <aside class="opl-sidebar" id="opl-sidebar" aria-label="Filtres de recherche">
        <div class="opl-sf-header">
            <span class="opl-sf-title">
                <i class="fas fa-sliders-h" aria-hidden="true"></i> Filtres
            </span>
            @if(request()->hasAny(['city','instant','featured','min_price','max_price']))
                <a href="{{ route('offers.index', request()->only(['search','category','sort','promo'])) }}"
                   class="opl-sf-reset">
                    <i class="fas fa-rotate-left" aria-hidden="true"></i> Réinitialiser
                </a>
            @endif
        </div>

        <form action="{{ route('offers.index') }}" method="GET" id="opl-filter-form">
            @foreach(request()->only(['search','category','sort','promo']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach

            {{-- Ville --}}
            <div class="opl-sf-block">
                <button type="button" class="opl-sf-toggle" onclick="oplToggleBlock(this)" aria-expanded="true">
                    <span><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Ville</span>
                    <i class="fas fa-chevron-down opl-sf-arrow" aria-hidden="true"></i>
                </button>
                <div class="opl-sf-options">
                    @foreach($cities as $city)
                    <label class="opl-sf-opt">
                        <input type="radio" name="city" value="{{ $city->id }}"
                               {{ request('city') == $city->id ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <span class="opl-sf-opt-dot"></span>
                        <span class="opl-sf-opt-label">{{ $city->name }}</span>
                        <span class="opl-sf-opt-count">{{ $city->offers_count }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Budget --}}
            <div class="opl-sf-block">
                <button type="button" class="opl-sf-toggle" onclick="oplToggleBlock(this)" aria-expanded="true">
                    <span><i class="fas fa-tag" aria-hidden="true"></i> Budget (FCFA)</span>
                    <i class="fas fa-chevron-down opl-sf-arrow" aria-hidden="true"></i>
                </button>
                <div class="opl-sf-options">
                    <div class="opl-price-range">
                        <div class="opl-price-input-wrap">
                            <span class="opl-price-label">Min</span>
                            <input type="number" name="min_price"
                                   value="{{ request('min_price') }}"
                                   placeholder="0" class="opl-price-input"
                                   aria-label="Prix minimum"
                                   onchange="this.form.submit()">
                        </div>
                        <span class="opl-price-sep" aria-hidden="true">—</span>
                        <div class="opl-price-input-wrap">
                            <span class="opl-price-label">Max</span>
                            <input type="number" name="max_price"
                                   value="{{ request('max_price') }}"
                                   placeholder="∞" class="opl-price-input"
                                   aria-label="Prix maximum"
                                   onchange="this.form.submit()">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Options --}}
            <div class="opl-sf-block">
                <button type="button" class="opl-sf-toggle" onclick="oplToggleBlock(this)" aria-expanded="true">
                    <span><i class="fas fa-bolt" aria-hidden="true"></i> Options</span>
                    <i class="fas fa-chevron-down opl-sf-arrow" aria-hidden="true"></i>
                </button>
                <div class="opl-sf-options">
                    <label class="opl-sf-opt">
                        <input type="checkbox" name="instant" value="1"
                               {{ request('instant') ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <span class="opl-sf-checkbox"></span>
                        <span class="opl-sf-opt-label">⚡ Réservation instantanée</span>
                    </label>
                    <label class="opl-sf-opt">
                        <input type="checkbox" name="featured" value="1"
                               {{ request('featured') ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <span class="opl-sf-checkbox"></span>
                        <span class="opl-sf-opt-label">⭐ Coups de cœur</span>
                    </label>
                    <label class="opl-sf-opt">
                        <input type="checkbox" name="promo" value="1"
                               {{ request('promo') ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <span class="opl-sf-checkbox"
                              style="border-color:rgba(192,50,26,.35)"></span>
                        <span class="opl-sf-opt-label">🔥 Offres promotionnelles</span>
                    </label>
                </div>
            </div>
        </form>

        <div class="opl-sf-trust" aria-label="Nos engagements">
            <div class="opl-sf-trust-item"><i class="fas fa-shield-alt" aria-hidden="true"></i> Guides certifiés</div>
            <div class="opl-sf-trust-item"><i class="fas fa-undo-alt" aria-hidden="true"></i> Annulation 48h</div>
            <div class="opl-sf-trust-item"><i class="fas fa-headset" aria-hidden="true"></i> Support 24/7</div>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <main id="opl-main" tabindex="-1">

        {{-- Barre résultats --}}
        <div class="opl-results-bar">
            <div class="opl-results-info">
                <strong>{{ $offers->total() }}</strong>
                expérience{{ $offers->total() > 1 ? 's' : '' }}
                @if(request('search'))
                    pour <em>"{{ request('search') }}"</em>
                @endif
                <span class="opl-results-page">
                    · Page {{ $offers->currentPage() }} / {{ $offers->lastPage() }}
                </span>
            </div>
            <div class="opl-results-actions">
                <select onchange="window.location = this.value" class="opl-sort-select" aria-label="Trier les résultats">
                    @foreach(['newest' => 'Plus récents', 'rating' => 'Mieux notés', 'price_asc' => 'Prix ↑', 'price_desc' => 'Prix ↓'] as $val => $label)
                    <option value="{{ route('offers.index', array_merge(request()->all(), ['sort' => $val, 'page' => 1])) }}"
                            {{ request('sort','newest') === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                <button class="opl-filter-mobile-btn"
                        onclick="oplOpenDrawer()"
                        aria-expanded="false"
                        aria-controls="opl-drawer">
                    <i class="fas fa-sliders-h" aria-hidden="true"></i> Filtres
                    @if(request()->hasAny(['city','instant','featured','min_price','max_price','promo']))
                        <span class="opl-filter-dot" aria-label="Filtres actifs"></span>
                    @endif
                </button>
            </div>
        </div>

        @if($offers->count() > 0 || $promoOffers->count() > 0)

        {{-- ════ ZONE PROMOS ════
             Requête séparée depuis le controller — affichée sur chaque page,
             disparaît si filtre promo actif (tout est déjà promo dans la grille) --}}
        @if($promoOffers->count() > 0 && !request('promo'))
        <section class="opl-promo-zone" aria-label="Offres promotionnelles">
            <div class="opl-promo-header">
                <div class="opl-promo-eyebrow">Offres à durée limitée</div>
                <h2 class="opl-promo-title">
                    Profitez avant
                    <span class="opl-promo-title-accent">qu'il soit trop tard</span>
                </h2>
                <p class="opl-promo-subtitle">
                    {{ $promoOffers->count() }}
                    expérience{{ $promoOffers->count() > 1 ? 's' : '' }} en promotion
                </p>
            </div>

            <div class="opl-promo-grid">
                @foreach($promoOffers as $idx => $offer)
                @php
                    $rCount     = (int)($offer->reviews_count ?? 0);
                    $hasReviews = $rCount >= 5;
                    $wishlisted = in_array($offer->id, $wishlistIds);
                    $discount   = round((1 - $offer->promotional_price / $offer->base_price) * 100);
                @endphp
                <article class="opl-promo-card"
                         style="animation-delay: {{ $idx * 0.08 }}s">

                    <a href="{{ route('offers.show', $offer->slug) }}"
                       class="opl-promo-card-img"
                       tabindex="-1" aria-hidden="true">
                        @if($offer->cover_image)
                            <img src="{{ mediaUrl($offer->cover_image) }}"
                                 alt="{{ $offer->title }}"
                                 loading="eager" width="600" height="450">
                        @else
                            @php $gr = $gradients[$offer->category] ?? ['#D4E8C8','#8BBF6E']; @endphp
                            <div class="opl-promo-card-img-ph"
                                 style="background:linear-gradient(135deg,{{ $gr[0] }},{{ $gr[1] }});">
                                {{ $emojis[$offer->category] ?? '✨' }}
                            </div>
                        @endif
                        <div class="opl-promo-card-img-overlay" aria-hidden="true"></div>
                        <div class="opl-discount-badge" aria-label="Réduction {{ $discount }}%">
                            −{{ $discount }}%
                        </div>
                    </a>

                    <div class="opl-promo-card-body">
                        <div>
                            <div class="opl-promo-card-location">
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                {{ $offer->city->name }} · {{ $offer->category_label }}
                            </div>
                            <h3 class="opl-promo-card-title">
                                <a href="{{ route('offers.show', $offer->slug) }}">{{ $offer->title }}</a>
                            </h3>
                            @if($offer->short_description)
                                <p class="opl-promo-card-desc">
                                    {{ Str::limit($offer->short_description, 120) }}
                                </p>
                            @endif
                            <div class="opl-promo-card-meta">
                                @if($hasReviews)
                                    <div class="opl-stars" aria-hidden="true">
                                        @php
                                        $litC = min((int)round($offer->average_rating ?? 0), 5);
                                        echo str_repeat('<i class="fas fa-star lit"></i>', $litC) . str_repeat('<i class="fas fa-star"></i>', 5 - $litC);
                                    @endphp
                                    </div>
                                    <span class="opl-promo-rating">
                                        {{ number_format($offer->average_rating ?? 0, 1) }}
                                        ({{ $rCount }})
                                    </span>
                                @else
                                    <span style="font-size:11px;color:var(--f-500);font-weight:600">✨ Nouveau</span>
                                @endif
                                <div class="opl-promo-duration">
                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                    @php
                                        $h = floor($offer->duration_minutes / 60);
                                        $m = $offer->duration_minutes % 60;
                                    @endphp
                                    {{ $h }}h{{ $m > 0 ? $m.'min' : '' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="opl-promo-price-from">À partir de</span>
                            <span class="opl-promo-price-old">
                                {{ number_format($offer->base_price, 0, '', ' ') }} FCFA
                            </span>
                            <div class="opl-promo-price-new">
                                {{ number_format($offer->promotional_price, 0, '', ' ') }}
                                <span class="opl-promo-currency">FCFA</span>
                            </div>
                            <div class="opl-promo-price-eur">
                                ≈ {{ number_format($offer->promotional_price / config('discovtrip.eur_rate', 655.957), 0, '', ' ') }} €
                            </div>
                            <a href="{{ route('offers.show', $offer->slug) }}" class="opl-promo-cta">
                                Réserver <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Wishlist --}}
                    @auth
                    <button class="opl-promo-wish-btn dt-wish-btn {{ $wishlisted ? 'active' : '' }}"
                            data-wishlist-id="{{ $offer->id }}"
                            aria-label="{{ $wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}">
                        <i class="{{ $wishlisted ? 'fas' : 'far' }} fa-heart" aria-hidden="true"></i>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="opl-promo-wish-btn" aria-label="Connexion requise">
                        <i class="far fa-heart" aria-hidden="true"></i>
                    </a>
                    @endauth

                </article>
                @endforeach
            </div>
        </section>
        @endif

        {{-- ════ CATALOGUE ════ --}}
        @if($offers->count() > 0)

        {{-- En-tête catalogue — affiché uniquement s'il y a aussi des promos --}}
        @if($promoOffers->count() > 0 && !request('promo'))
        <div class="opl-catalog-header">
            <div class="opl-catalog-title-wrap">
                <div class="opl-catalog-line" aria-hidden="true"></div>
                <h2 class="opl-catalog-title">Toutes les expériences</h2>
            </div>
            <span class="opl-catalog-count">
                {{ $offers->total() }} expérience{{ $offers->total() > 1 ? 's' : '' }}
            </span>
        </div>
        @endif

        <div class="opl-grid" id="opl-grid" role="list">
            @foreach($offers as $index => $offer)
            @php
                $rCount     = (int)($offer->reviews_count ?? 0);
                $hasReviews = $rCount >= 5;
                $avgRating  = round($offer->reviews_avg_rating ?? 0, 1);
                $isUrgent   = isset($offer->available_spots) && $offer->available_spots > 0 && $offer->available_spots <= 5;
                $isSoldOut  = isset($offer->available_spots) && $offer->available_spots === 0;
                $wishlisted = in_array($offer->id, $wishlistIds);
                $showScoreBar = $hasReviews && $avgRating >= 4.8;
            @endphp

            <article class="opl-card {{ $isSoldOut ? 'soldout' : '' }} dt-reveal"
                     style="--reveal-delay: {{ ($index % 9) * 0.06 }}s"
                     role="listitem"
                     @if($isSoldOut) aria-label="{{ $offer->title }} — Complet" @endif>

                {{-- Wishlist --}}
                @auth
                <button class="opl-wish-btn dt-wish-btn {{ $wishlisted ? 'active' : '' }}"
                        data-wishlist-id="{{ $offer->id }}"
                        aria-label="{{ $wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                        @if($isSoldOut) disabled @endif>
                    <i class="{{ $wishlisted ? 'fas' : 'far' }} fa-heart" aria-hidden="true"></i>
                </button>
                @else
                <a href="{{ route('login') }}" class="opl-wish-btn" aria-label="Connexion requise">
                    <i class="far fa-heart" aria-hidden="true"></i>
                </a>
                @endauth

                {{-- Image --}}
                <a href="{{ $isSoldOut ? '#' : route('offers.show', $offer->slug) }}"
                   class="opl-card-img-wrap"
                   tabindex="-1" aria-hidden="true"
                   @if($isSoldOut) style="cursor:default" @endif>
                    @if($offer->cover_image)
                        <img src="{{ mediaUrl($offer->cover_image) }}"
                             alt="{{ $offer->title }}"
                             class="opl-card-img"
                             loading="{{ $index < 3 ? 'eager' : 'lazy' }}"
                             width="600" height="450">
                    @else
                        @php $gr = $gradients[$offer->category] ?? ['#D4E8C8','#8BBF6E']; @endphp
                        <div class="opl-card-img opl-card-img-ph"
                             style="background:linear-gradient(135deg,{{ $gr[0] }},{{ $gr[1] }});"
                             aria-hidden="true">
                            {{ $emojis[$offer->category] ?? '✨' }}
                        </div>
                    @endif
                    <div class="opl-card-img-overlay" aria-hidden="true"></div>

                    @if($isSoldOut)
                        <div class="opl-soldout-badge">Complet · Bientôt disponible</div>
                    @else
                        <div class="opl-card-badges">
                            @if($offer->is_featured)
                                <span class="opl-badge opl-badge-featured">⭐ Coup de cœur</span>
                            @elseif($rCount < 5)
                                <span class="opl-badge opl-badge-new">✨ Nouveau</span>
                            @endif
                            @if($offer->is_instant_booking)
                                <span class="opl-badge opl-badge-instant" title="Réservation instantanée">⚡</span>
                            @endif
                        </div>
                        @if($isUrgent)
                        <div class="opl-urgency" aria-live="polite">
                            <i class="fas fa-fire" aria-hidden="true"></i>
                            {{ $offer->available_spots }}
                            place{{ $offer->available_spots > 1 ? 's' : '' }}
                        </div>
                        @endif
                    @endif
                </a>

                {{-- Corps --}}
                <div class="opl-card-body">
                    <div class="opl-card-location">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        {{ $offer->city->name }}
                        <span>· {{ $offer->category_label }}</span>
                    </div>

                    <h3 class="opl-card-title">
                        @if($isSoldOut)
                            {{ $offer->title }}
                        @else
                            <a href="{{ route('offers.show', $offer->slug) }}">{{ $offer->title }}</a>
                        @endif
                    </h3>

                    <div class="opl-card-meta">
                        <div class="opl-card-rating">
                            @if($hasReviews)
                                <div class="opl-rating-row">
                                    <div class="opl-stars" aria-hidden="true">
                                        @php
                                        $litC = min((int)round($avgRating), 5);
                                        echo str_repeat('<i class="fas fa-star lit"></i>', $litC) . str_repeat('<i class="fas fa-star"></i>', 5 - $litC);
                                    @endphp
                                    </div>
                                    <strong>{{ number_format($avgRating, 1) }}</strong>
                                    <span style="color:var(--tx-soft)">({{ $rCount }})</span>
                                </div>
                                @if($showScoreBar)
                                <div class="opl-score-bar" aria-hidden="true">
                                    <div class="opl-score-bar-fill"
                                         data-width="{{ round($avgRating / 5 * 100) }}%"
                                         style="width:0"></div>
                                </div>
                                @endif
                            @else
                                <span class="opl-new-tag">✨ Nouveau partenaire</span>
                            @endif
                        </div>
                        <div class="opl-card-duration">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            @php
                                $h = floor($offer->duration_minutes / 60);
                                $m = $offer->duration_minutes % 60;
                            @endphp
                            {{ $h }}h{{ $m > 0 ? $m.'min' : '' }}
                        </div>
                    </div>

                    <div class="opl-card-footer">
                        <div class="opl-card-price-block">
                            <span class="opl-price-from">À partir de</span>
                            <div class="opl-price">
                                {{ number_format($offer->base_price, 0, '', ' ') }}
                                <span class="opl-currency">FCFA</span>
                            </div>
                            <div class="opl-price-eur">
                                ≈ {{ number_format($offer->base_price / config('discovtrip.eur_rate', 655.957), 0, '', ' ') }} €
                            </div>
                        </div>
                        @if($isSoldOut)
                            <span class="opl-card-cta" aria-disabled="true">Complet</span>
                        @else
                            <a href="{{ route('offers.show', $offer->slug) }}" class="opl-card-cta">
                                Réserver <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </article>
            @endforeach
        </div>

        {{-- ── PAGINATION ── --}}
        @if($offers->hasPages())
        <nav class="opl-pagination" aria-label="Pagination des résultats">
            <div class="opl-pag-info">
                Affichage de {{ $offers->firstItem() }}–{{ $offers->lastItem() }}
                sur {{ $offers->total() }} expériences
            </div>
            <div class="opl-pag-controls">
                @if($offers->onFirstPage())
                    <span class="opl-pag-btn opl-pag-btn-disabled" aria-disabled="true">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </span>
                @else
                    <a href="{{ $offers->previousPageUrl() }}"
                       class="opl-pag-btn" aria-label="Page précédente">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </a>
                @endif

                @php
                    $start = max(1, $offers->currentPage() - 2);
                    $end   = min($offers->lastPage(), $offers->currentPage() + 2);
                @endphp

                @if($start > 1)
                    <a href="{{ $offers->url(1) }}" class="opl-pag-btn">1</a>
                    @if($start > 2)
                        <span class="opl-pag-dots" aria-hidden="true">…</span>
                    @endif
                @endif

                @php $p = $start; @endphp
                @while($p <= $end)
                    @if($p === $offers->currentPage())
                        <span class="opl-pag-btn opl-pag-btn-active" aria-current="page">{{ $p }}</span>
                    @else
                        <a href="{{ $offers->url($p) }}"
                           class="opl-pag-btn" aria-label="Page {{ $p }}">{{ $p }}</a>
                    @endif
                
                @php $p++; @endphp
                @endwhile

                @if($end < $offers->lastPage())
                    @if($end < $offers->lastPage() - 1)
                        <span class="opl-pag-dots" aria-hidden="true">…</span>
                    @endif
                    <a href="{{ $offers->url($offers->lastPage()) }}" class="opl-pag-btn">
                        {{ $offers->lastPage() }}
                    </a>
                @endif

                @if($offers->hasMorePages())
                    <a href="{{ $offers->nextPageUrl() }}"
                       class="opl-pag-btn" aria-label="Page suivante">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </a>
                @else
                    <span class="opl-pag-btn opl-pag-btn-disabled" aria-disabled="true">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </span>
                @endif
            </div>
        </nav>
        @endif

        @endif {{-- offers->count() > 0 --}}

        @else
        {{-- ════ ÉTAT VIDE CONTEXTUEL ════ --}}
        <div class="opl-empty" role="status">
            <div class="opl-empty-icon" aria-hidden="true">🔍</div>
            <h3>
                @if(request('search'))
                    Aucun résultat pour «&nbsp;{{ request('search') }}&nbsp;»
                @else
                    Aucune expérience trouvée
                @endif
            </h3>
            <p>
                @if(request('search'))
                    Essayez un autre terme ou explorez nos catégories&nbsp;:
                @else
                    Modifiez vos filtres ou explorez toutes nos expériences.
                @endif
            </p>
            @if(request('search'))
            <div class="opl-empty-cats">
                @foreach(array_filter(array_keys($categories)) as $cat)
                <a href="{{ route('offers.index', ['category' => $cat]) }}" class="opl-empty-cat">
                    {{ $categories[$cat]['icon'] }} {{ $categories[$cat]['label'] }}
                </a>
                @endforeach
            </div>
            @endif
            <a href="{{ route('offers.index') }}" class="opl-empty-cta">
                Voir toutes les expériences
            </a>
        </div>
        @endif

    </main>
</div>

{{-- ════ TRUST MOBILE ════ --}}
<div class="opl-trust-mobile" aria-label="Nos engagements">
    <div class="opl-trust-mobile-item"><i class="fas fa-shield-alt"></i> Guides certifiés</div>
    <div class="opl-trust-mobile-item"><i class="fas fa-undo-alt"></i> Annulation jusqu'à 48h avant</div>
    <div class="opl-trust-mobile-item"><i class="fas fa-headset"></i> Support disponible 24h/24</div>
</div>

{{-- ════ MOBILE DRAWER ════ --}}
<div class="opl-overlay" id="opl-overlay" onclick="oplCloseDrawer()" aria-hidden="true"></div>
<div class="opl-drawer" id="opl-drawer" role="dialog" aria-modal="true" aria-label="Filtres">
    <div class="opl-drawer-header">
        <span class="opl-drawer-title">
            <i class="fas fa-sliders-h"></i> Filtres
        </span>
        <button onclick="oplCloseDrawer()" class="opl-drawer-close" aria-label="Fermer">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>
    <div class="opl-drawer-body">
        <form action="{{ route('offers.index') }}" method="GET" id="opl-drawer-form">
            @foreach(request()->only(['search','category']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach

            <div class="opl-drawer-section">
                <div class="opl-drawer-section-title">Ville</div>
                @foreach($cities as $city)
                <label class="opl-sf-opt">
                    <input type="radio" name="city" value="{{ $city->id }}"
                           {{ request('city') == $city->id ? 'checked' : '' }}>
                    <span class="opl-sf-opt-dot"></span>
                    <span class="opl-sf-opt-label">{{ $city->name }}</span>
                    <span class="opl-sf-opt-count">{{ $city->offers_count }}</span>
                </label>
                @endforeach
            </div>

            <div class="opl-drawer-section">
                <div class="opl-drawer-section-title">Trier par</div>
                @foreach(['newest' => 'Plus récents', 'rating' => 'Mieux notés', 'price_asc' => 'Prix croissant', 'price_desc' => 'Prix décroissant'] as $val => $label)
                <label class="opl-sf-opt">
                    <input type="radio" name="sort" value="{{ $val }}"
                           {{ request('sort','newest') === $val ? 'checked' : '' }}>
                    <span class="opl-sf-opt-dot"></span>
                    <span class="opl-sf-opt-label">{{ $label }}</span>
                </label>
                @endforeach
            </div>

            <div class="opl-drawer-section">
                <div class="opl-drawer-section-title">Options</div>
                <label class="opl-sf-opt">
                    <input type="checkbox" name="instant" value="1" {{ request('instant') ? 'checked' : '' }}>
                    <span class="opl-sf-checkbox"></span>
                    <span class="opl-sf-opt-label">⚡ Réservation instantanée</span>
                </label>
                <label class="opl-sf-opt">
                    <input type="checkbox" name="promo" value="1" {{ request('promo') ? 'checked' : '' }}>
                    <span class="opl-sf-checkbox" style="border-color:rgba(192,50,26,.35)"></span>
                    <span class="opl-sf-opt-label">🔥 Offres promotionnelles</span>
                </label>
            </div>

            <div class="opl-drawer-footer">
                <a href="{{ route('offers.index') }}" class="opl-drawer-reset">Réinitialiser</a>
                <button type="submit" class="opl-drawer-apply">
                    <i class="fas fa-check" aria-hidden="true"></i> Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Compteur animé ── */
(function () {
    const el = document.getElementById('opl-hero-counter');
    if (!el) return;
    const target = parseInt(el.dataset.target || el.textContent);
    if (target < 2) return;
    let v = 0;
    const dur = 900, fps = 60;
    const inc = target / (dur / (1000 / fps));
    const t = setInterval(() => {
        v = Math.min(v + inc, target);
        el.textContent = Math.round(v);
        if (v >= target) clearInterval(t);
    }, 1000 / fps);
})();

/* ── Score bars ── */
document.querySelectorAll('.opl-score-bar-fill[data-width]').forEach(bar => {
    setTimeout(() => { bar.style.width = bar.dataset.width; }, 500);
});

/* ── Scroll vers main après filtre ── */
(function () {
    if (document.referrer && new URL(document.referrer).pathname === window.location.pathname) {
        const main = document.getElementById('opl-main');
        if (main) setTimeout(() => main.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
    }
})();

/* ── Sidebar toggle ── */
function oplToggleBlock(btn) {
    const block = btn.closest('.opl-sf-block');
    block.classList.toggle('collapsed');
    btn.setAttribute('aria-expanded', !block.classList.contains('collapsed'));
}

/* ── Mobile drawer ── */
function oplOpenDrawer() {
    document.getElementById('opl-drawer').classList.add('open');
    document.getElementById('opl-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
    document.querySelector('.opl-filter-mobile-btn')?.setAttribute('aria-expanded', 'true');
}
function oplCloseDrawer() {
    document.getElementById('opl-drawer').classList.remove('open');
    document.getElementById('opl-overlay').classList.remove('open');
    document.body.style.overflow = '';
    document.querySelector('.opl-filter-mobile-btn')?.setAttribute('aria-expanded', 'false');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') oplCloseDrawer(); });

/* ── Wishlist AJAX ── */
function oplWishToast(msg) {
    let t = document.getElementById('opl-wish-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'opl-wish-toast';
        t.style.cssText = [
            'position:fixed', 'bottom:24px', 'left:50%',
            'transform:translateX(-50%)',
            'background:rgba(28,27,22,.93)', 'color:white',
            'font-size:13px', 'font-weight:700',
            'padding:11px 20px', 'border-radius:12px',
            'z-index:9999', 'border:1px solid rgba(212,162,15,.25)',
            'backdrop-filter:blur(8px)',
            'box-shadow:0 8px 24px rgba(0,0,0,.25)',
            'white-space:nowrap', 'transition:opacity .3s',
            'pointer-events:none'
        ].join(';');
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.display = 'block'; t.style.opacity = '1';
    clearTimeout(t._t);
    t._t = setTimeout(() => {
        t.style.opacity = '0';
        setTimeout(() => t.style.display = 'none', 300);
    }, 2400);
}

document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', async e => {
        e.preventDefault(); e.stopPropagation();
        if (btn.disabled) return;
        let wishlisted = btn.dataset.wishlisted === '1';
        wishlisted = !wishlisted;
        btn.dataset.wishlisted = wishlisted ? '1' : '0';
        const icon = btn.querySelector('i');
        if (icon) icon.className = (wishlisted ? 'fas' : 'far') + ' fa-heart';
        btn.classList.toggle('active', wishlisted);
        btn.setAttribute('aria-label', wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris');
        btn.style.transform = 'scale(1.4)';
        setTimeout(() => btn.style.transform = '', 240);
        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ offer_id: btn.dataset.offerId }),
            });
            if (!res.ok) throw new Error();
            oplWishToast(wishlisted ? '❤️ Ajouté aux favoris' : '🗑️ Retiré des favoris');
        } catch {
            wishlisted = !wishlisted;
            btn.dataset.wishlisted = wishlisted ? '1' : '0';
            if (icon) icon.className = (wishlisted ? 'fas' : 'far') + ' fa-heart';
            btn.classList.toggle('active', wishlisted);
            oplWishToast('⚠️ Erreur — réessayez');
        }
    });
});
</script>
@endpush