@extends('layouts.app')

@section('title', $city->name . ' — Expériences & Activités | DiscovTrip')

@push('meta')
<meta name="description" content="Découvrez {{ $city->name }} au Bénin : {{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }} uniques avec des guides locaux certifiés.{{ $city->description ? ' '.Str::limit(strip_tags($city->description), 120) : '' }}">
<meta property="og:title" content="{{ $city->name }} — Expériences & Activités | DiscovTrip">
<meta property="og:description" content="{{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }} à découvrir à {{ $city->name }}, Bénin.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $city->cover_image ? mediaUrl($city->cover_image) : asset('images/og-default.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{ $city->cover_image ? mediaUrl($city->cover_image) : asset('images/og-default.jpg') }}">
@endpush

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "TouristDestination",
            "name": "{{ $city->name }}",
            "description": "{{ Str::limit(strip_tags($city->description ?? ''), 200) }}",
            "url": "{{ url()->current() }}",
            "image": "{{ $city->cover_image ? mediaUrl($city->cover_image) : asset('images/og-default.jpg') }}",
            "touristType": ["Culturel", "Aventure", "Nature"],
            "address": {
                "@@type": "PostalAddress",
                "addressLocality": "{{ $city->name }}",
                "addressCountry": "BJ"
            }
            @if($city->latitude && $city->longitude)
            ,"geo": {
                "@@type": "GeoCoordinates",
                "latitude": {{ $city->latitude }},
                "longitude": {{ $city->longitude }}
            }
            @endif
            @if(isset($offers) && $offers->count() > 0)
            ,"containsPlace": [
                @foreach($offers->take(5) as $i => $offer)
                {
                    "@@type": "TouristAttraction",
                    "name": "{{ addslashes($offer->title) }}",
                    "url": "{{ route('offers.show', $offer->slug) }}"
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
            @endif
        },
        {
            "@@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@@type": "ListItem",
                    "position": 1,
                    "name": "Accueil",
                    "item": "{{ url('/') }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 2,
                    "name": "Destinations",
                    "item": "{{ url('/destinations') }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 3,
                    "name": "{{ $city->name }}",
                    "item": "{{ url()->current() }}"
                }
            ]
        }
    ]
}
</script>
@endpush

@push('styles')
    @vite('resources/css/pages/city.css')
@endpush

@php
$categoryEmojis = ['urban'=>'🏙️','historical'=>'🏛️','nature'=>'🌿','coastal'=>'🏖️'];
$categoryLabels = [
    'cultural'=>'Culture','gastronomy'=>'Gastronomie','nature'=>'Nature',
    'adventure'=>'Aventure','wellness'=>'Bien-être','urban'=>'Urbain',
    'historical'=>'Historique','coastal'=>'Côtière',
];
$offerEmojis = [
    'cultural'=>'🕌','gastronomy'=>'🍽️','nature'=>'🌊',
    'adventure'=>'🏔️','wellness'=>'🧘','urban'=>'🏙️',
];
$destGrads = [
    'Cotonou'=>'160deg,#1F6B44,#0D3822','Ganvié'=>'160deg,#1a4a6e,#0d2a42',
    'Ouidah'=>'160deg,#6B1F5A,#3D0D33','Abomey'=>'160deg,#4a6e1a,#2a420d',
    'Grand-Popo'=>'160deg,#6e4a1a,#42280d','Porto-Novo'=>'160deg,#1a3a6e,#0d1e42',
    'Parakou'=>'160deg,#1F6B44,#0D3822','Natitingou'=>'160deg,#6e3a1a,#42200d',
];

$cityEmoji = $categoryEmojis[$city->category ?? 'urban'] ?? '📍';
$cityGrad  = $destGrads[$city->name] ?? '160deg,#1F6B44,#0D3822';
$avgRating = $city->average_rating > 0 ? number_format($city->average_rating, 1) : '4.8';
$minPrice  = $city->offers_min_base_price;
@endphp

@section('content')

{{-- ════════════════════════════════════════════
     §1 HERO — plein écran, overlay dramatique
════════════════════════════════════════════ --}}
<section class="cy-hero">

    {{-- Fond --}}
    <div class="cy-hero-bg">
        @if($city->cover_image)
            <img src="{{ mediaUrl($city->cover_image) }}"
                 alt="{{ $city->name }}, Bénin"
                 loading="eager" class="cy-hero-bg-img">
        @elseif($heroOffer?->cover_image)
            <img src="{{ mediaUrl($heroOffer->cover_image) }}"
                 alt="{{ $city->name }}, Bénin"
                 loading="eager" class="cy-hero-bg-img">
        @else
            <div class="cy-hero-bg-ph" style="background:linear-gradient({{ $cityGrad }})">
                <span>{{ $cityEmoji }}</span>
            </div>
        @endif
        <div class="cy-hero-overlay"></div>
    </div>

    {{-- Breadcrumb --}}
    <nav class="cy-breadcrumb" aria-label="Fil d'ariane">
        <a href="{{ route('home') }}">Accueil</a>
        <i class="fas fa-chevron-right" aria-hidden="true"></i>
        <a href="{{ route('destinations') }}">Destinations</a>
        <i class="fas fa-chevron-right" aria-hidden="true"></i>
        <span>{{ $city->name }}</span>
    </nav>

    {{-- Contenu --}}
    <div class="dt-container cy-hero-inner">

        <div class="cy-hero-left">

            {{-- Tags --}}
            <div class="cy-hero-tags">
                @if($city->is_featured)
                    <span class="cy-tag cy-tag--gold">
                        <i class="fas fa-star" aria-hidden="true"></i> À la une
                    </span>
                @endif
                @if($city->category)
                    <span class="cy-tag">{{ $categoryEmojis[$city->category] ?? '📍' }} {{ ucfirst($city->category) }}</span>
                @endif
                @if($city->best_season ?? null)
                    <span class="cy-tag">
                        <i class="fas fa-sun" aria-hidden="true"></i> {{ $city->best_season }}
                    </span>
                @endif
            </div>

            <h1 class="cy-hero-title">{{ $city->name }}</h1>

            @if($city->description)
                <p class="cy-hero-desc">{{ $city->description }}</p>
            @endif

            <a href="#cy-offers" class="cy-hero-cta">
                <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                Voir les {{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }}
                <span class="cy-cta-arrow"><i class="fas fa-arrow-down" aria-hidden="true"></i></span>
            </a>
        </div>

        {{-- Stats --}}
        <div class="cy-hero-stats">
            <div class="cy-stat">
                <span class="cy-stat-num">{{ $city->offers_count }}</span>
                <span class="cy-stat-lbl">Expériences</span>
            </div>
            <div class="cy-stat-div"></div>
            <div class="cy-stat">
                <span class="cy-stat-num">{{ $avgRating }}</span>
                <span class="cy-stat-lbl">Note ⭐</span>
            </div>
            <div class="cy-stat-div"></div>
            <div class="cy-stat">
                @if($minPrice)
                    <span class="cy-stat-num cy-stat-num--sm">{{ number_format($minPrice, 0, ',', ' ') }}</span>
                    <span class="cy-stat-lbl">FCFA min</span>
                @else
                    <span class="cy-stat-num">—</span>
                    <span class="cy-stat-lbl">Prix</span>
                @endif
            </div>
        </div>

    </div>

    {{-- Scroll indicator --}}
    <div class="cy-scroll-hint" aria-hidden="true">
        <div class="cy-scroll-dot"></div>
    </div>
</section>


{{-- ════════════════════════════════════════════
     §2 META BAR — sticky
════════════════════════════════════════════ --}}
<div class="cy-meta-bar" id="cy-meta-bar">
    <div class="dt-container cy-meta-inner">

        @if($city->distance_from_cotonou ?? null)
        <div class="cy-meta-chip">
            <span class="cy-meta-icon"><i class="fas fa-car" aria-hidden="true"></i></span>
            <div>
                <span class="cy-meta-val">{{ $city->distance_from_cotonou }}</span>
                <span class="cy-meta-lbl">De Cotonou</span>
            </div>
        </div>
        @endif

        @if($city->duration_days ?? null)
        <div class="cy-meta-chip">
            <span class="cy-meta-icon"><i class="fas fa-clock" aria-hidden="true"></i></span>
            <div>
                <span class="cy-meta-val">{{ $city->duration_days }}</span>
                <span class="cy-meta-lbl">Séjour recommandé</span>
            </div>
        </div>
        @endif

        @if($city->best_season ?? null)
        <div class="cy-meta-chip">
            <span class="cy-meta-icon"><i class="fas fa-sun" aria-hidden="true"></i></span>
            <div>
                <span class="cy-meta-val">{{ $city->best_season }}</span>
                <span class="cy-meta-lbl">Meilleure saison</span>
            </div>
        </div>
        @endif

        <div class="cy-meta-chip">
            <span class="cy-meta-icon"><i class="fas fa-star" aria-hidden="true"></i></span>
            <div>
                <span class="cy-meta-val">{{ $avgRating }} / 5</span>
                <span class="cy-meta-lbl">Note moyenne</span>
            </div>
        </div>

        <div class="cy-meta-chip">
            <span class="cy-meta-icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></span>
            <div>
                <span class="cy-meta-val">Annulation gratuite</span>
                <span class="cy-meta-lbl">Jusqu'à 48h avant</span>
            </div>
        </div>

        {{-- CTA sticky (visible quand scrollé) --}}
        <a href="#cy-offers" class="cy-meta-cta">
            <i class="fas fa-ticket-alt" aria-hidden="true"></i>
            Voir les expériences
        </a>

    </div>
</div>


{{-- ════════════════════════════════════════════
     §3 OFFRES
════════════════════════════════════════════ --}}
<section class="cy-offers" id="cy-offers">
    <div class="dt-container">

        {{-- Header --}}
        <div class="cy-offers-head dt-reveal">
            <div>
                <div class="cy-section-label">
                    <span class="cy-label-bar"></span>
                    <span>{{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }}</span>
                </div>
                <h2 class="cy-section-title">À découvrir à <em>{{ $city->name }}</em></h2>
            </div>

            {{-- Filtres --}}
            {{-- Suppression de cy-anim-d2 (classe non définie en CSS) --}}
            <div class="cy-offers-controls dt-reveal">
                <div class="cy-filter-bar">
                    <a href="{{ route('destinations.city', $city->slug) }}"
                       class="cy-filter-btn {{ !request('category') ? 'cy-filter-btn--active' : '' }}">
                        Tout
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('destinations.city', [$city->slug, 'category' => $cat]) }}"
                           class="cy-filter-btn {{ request('category') === $cat ? 'cy-filter-btn--active' : '' }}">
                            {{ $offerEmojis[$cat] ?? '' }} {{ $categoryLabels[$cat] ?? ucfirst($cat) }}
                        </a>
                    @endforeach
                </div>

                @php $currentParams = array_filter(request()->only(['category'])); @endphp
                <select class="cy-sort-select" onchange="window.location=this.value" aria-label="Trier les offres">
                    <option value="{{ route('destinations.city', [$city->slug] + $currentParams) }}"
                        {{ !request('sort') ? 'selected' : '' }}>Mis en avant</option>
                    <option value="{{ route('destinations.city', [$city->slug] + $currentParams + ['sort'=>'price_asc']) }}"
                        {{ request('sort')==='price_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="{{ route('destinations.city', [$city->slug] + $currentParams + ['sort'=>'price_desc']) }}"
                        {{ request('sort')==='price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                    <option value="{{ route('destinations.city', [$city->slug] + $currentParams + ['sort'=>'rating']) }}"
                        {{ request('sort')==='rating' ? 'selected' : '' }}>Mieux notées</option>
                </select>
            </div>
        </div>

        {{-- Grille offres --}}
        <div class="cy-offers-grid">
            @forelse($offers as $i => $offer)
            @php
                $offerEmoji  = $offerEmojis[$offer->category] ?? '✨';
                $avgOfferRat = ($offer->reviews_avg_rating ?? 0) > 0
                    ? number_format($offer->reviews_avg_rating, 1)
                    : null;
                // Durée : null check
                $durationH = $offer->duration_minutes ? floor($offer->duration_minutes / 60) : 0;
                $durationM = $offer->duration_minutes ? ($offer->duration_minutes % 60) : 0;
                $durationStr = $durationH > 0 ? $durationH.'h' : '';
                $durationStr .= $durationM > 0 ? $durationM.'min' : '';
                if (!$durationStr) $durationStr = 'N/C';
            @endphp

            <a href="{{ route('offers.show', $offer->slug) }}"
               class="cy-offer-card dt-reveal"
               style="--delay:{{ ($i % 3) * 0.08 }}s">

                {{-- Image --}}
                <div class="cy-oc-img">
                    @if($offer->cover_image)
                        <img src="{{ mediaUrl($offer->cover_image) }}"
                             alt="{{ $offer->title }}" loading="lazy" class="cy-oc-img-src">
                    @else
                        <div class="cy-oc-img-ph">
                            <span>{{ $offerEmoji }}</span>
                        </div>
                    @endif
                    <div class="cy-oc-img-overlay"></div>

                    {{-- Badges --}}
                    <div class="cy-oc-badges">
                        @if($offer->category)
                            <span class="cy-oc-badge-cat">{{ $categoryLabels[$offer->category] ?? ucfirst($offer->category) }}</span>
                        @endif
                        @if($offer->is_instant_booking)
                            <span class="cy-oc-badge-instant">
                                <i class="fas fa-bolt" aria-hidden="true"></i> Instantané
                            </span>
                        @endif
                    </div>

                    {{-- Prix --}}
                    <div class="cy-oc-price">
                        <span class="cy-oc-price-val">{{ number_format($offer->base_price, 0, ',', ' ') }}</span>
                        <span class="cy-oc-price-cur">FCFA</span>
                        <span class="cy-oc-price-per">/ pers.</span>
                    </div>

                    {{-- Urgence --}}
                    @if(($offer->available_spots ?? 0) > 0 && $offer->available_spots <= 3)
                    <div class="cy-oc-urgent">
                        <i class="fas fa-fire" aria-hidden="true"></i>
                        {{ $offer->available_spots }} place{{ $offer->available_spots > 1 ? 's' : '' }} restante{{ $offer->available_spots > 1 ? 's' : '' }}
                    </div>
                    @endif
                </div>

                {{-- Corps --}}
                <div class="cy-oc-body">
                    @if($avgOfferRat)
                    <div class="cy-oc-rating">
                        <div class="cy-oc-stars" aria-label="Note {{ $avgOfferRat }} sur 5">
                            @php
                                $onCount  = min((int)round($avgOfferRat ?? 0), 5);
                                $offCount = 5 - $onCount;
                                echo str_repeat('<i class="fas fa-star cy-star-on" aria-hidden="true"></i>', $onCount);
                                echo str_repeat('<i class="fas fa-star cy-star-off" aria-hidden="true"></i>', $offCount);
                            @endphp
                        </div>
                        <span class="cy-oc-score">{{ $avgOfferRat }}</span>
                        <span class="cy-oc-reviews">({{ $offer->reviews_count }} avis)</span>
                    </div>
                    @endif

                    <h3 class="cy-oc-title">{{ $offer->title }}</h3>

                    @if($offer->short_description ?? $offer->description ?? null)
                        <p class="cy-oc-desc">{{ Str::limit($offer->short_description ?? $offer->description, 95) }}</p>
                    @endif

                    <div class="cy-oc-meta">
                        <span class="cy-oc-meta-item">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            {{ $durationStr }}
                        </span>
                        @if($offer->max_participants ?? null)
                        <span class="cy-oc-meta-item">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            Max {{ $offer->max_participants }}
                        </span>
                        @endif
                        @if($offer->difficulty_level ?? null)
                        <span class="cy-oc-meta-item">
                            <i class="fas fa-signal" aria-hidden="true"></i>
                            {{ ucfirst($offer->difficulty_level) }}
                        </span>
                        @endif
                    </div>

                    <div class="cy-oc-footer">
                        <span class="cy-oc-note">
                            @if($offer->is_instant_booking)
                                <i class="fas fa-bolt" aria-hidden="true"></i> Confirmation immédiate
                            @else
                                <i class="fas fa-check-circle" aria-hidden="true"></i> Annulation gratuite
                            @endif
                        </span>
                        <span class="cy-oc-cta">
                            Réserver <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </a>
            @empty
            <div class="cy-offers-empty">
                <div class="cy-offers-empty-icon">🔍</div>
                <p>Aucune expérience dans cette catégorie.</p>
                <a href="{{ route('destinations.city', $city->slug) }}" class="cy-clear-filter">
                    Voir toutes les expériences
                </a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($offers->hasPages())
        <div class="cy-pagination">{{ $offers->links() }}</div>
        @endif

    </div>
</section>


{{-- ════════════════════════════════════════════
     §4 HIGHLIGHTS — pourquoi y aller
════════════════════════════════════════════ --}}
@if(($city->highlights ?? null) && count($city->highlights) > 0)
<section class="cy-highlights">
    <div class="dt-container">

        <div class="cy-section-head dt-reveal">
            <div class="cy-section-label">
                <span class="cy-label-bar"></span>
                <span>Pourquoi y aller</span>
            </div>
            <h2 class="cy-section-title">{{ $city->name }}, <em>c'est avant tout…</em></h2>
        </div>

        <div class="cy-highlights-grid">
            @foreach($city->highlights as $i => $h)
            <div class="cy-highlight-card dt-reveal" style="--delay:{{ $i * 0.1 }}s">
                <div class="cy-hl-num" aria-hidden="true">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</div>
                <div class="cy-hl-icon">
                    <i class="fas fa-{{ $h['icon'] ?? 'star' }}" aria-hidden="true"></i>
                </div>
                <h3 class="cy-hl-title">{{ $h['title'] }}</h3>
                <p class="cy-hl-desc">{{ $h['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ════════════════════════════════════════════
     §5 LANDMARKS — les incontournables
════════════════════════════════════════════ --}}
@if(($city->landmarks ?? null) && count($city->landmarks) > 0)
<section class="cy-landmarks">
    <div class="dt-container">

        <div class="cy-section-head dt-reveal">
            <div class="cy-section-label">
                <span class="cy-label-bar"></span>
                <span>À ne pas manquer</span>
            </div>
            <h2 class="cy-section-title">Les <em>incontournables</em></h2>
        </div>

        <div class="cy-landmarks-grid">
            @foreach($city->landmarks as $i => $lm)
            <div class="cy-landmark dt-reveal" style="--delay:{{ ($i % 4) * 0.08 }}s">
                <div class="cy-landmark-emoji">{{ $lm['emoji'] ?? '📍' }}</div>
                <div class="cy-landmark-body">
                    <h3 class="cy-landmark-name">{{ $lm['name'] }}</h3>
                    @if($lm['description'] ?? null)
                        <p class="cy-landmark-desc">{{ $lm['description'] }}</p>
                    @endif
                </div>
                <div class="cy-landmark-arrow"><i class="fas fa-arrow-right" aria-hidden="true"></i></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ════════════════════════════════════════════
     §6 INFOS PRATIQUES + FUN FACTS
════════════════════════════════════════════ --}}
@if(($city->how_to_get_there ?? null) || ($city->best_time_detail ?? null) || ($city->budget_range ?? null) || (($city->fun_facts ?? null) && count($city->fun_facts) > 0))
<section class="cy-infos">
    <div class="dt-container cy-infos-grid">

        {{-- Infos pratiques --}}
        @if(($city->how_to_get_there ?? null) || ($city->best_time_detail ?? null) || ($city->budget_range ?? null))
        <div class="dt-reveal">
            <div class="cy-section-label">
                <span class="cy-label-bar"></span>
                <span>Infos pratiques</span>
            </div>
            <h2 class="cy-section-title" style="margin-bottom:32px;">Préparez <em>votre séjour</em></h2>

            <div class="cy-info-list">
                @if($city->how_to_get_there ?? null)
                <div class="cy-info-item">
                    <div class="cy-info-icon"><i class="fas fa-car" aria-hidden="true"></i></div>
                    <div>
                        <div class="cy-info-label">Comment y aller</div>
                        <div class="cy-info-val">{{ $city->how_to_get_there }}</div>
                    </div>
                </div>
                @endif
                @if($city->best_time_detail ?? null)
                <div class="cy-info-item">
                    <div class="cy-info-icon"><i class="fas fa-sun" aria-hidden="true"></i></div>
                    <div>
                        <div class="cy-info-label">Meilleure période</div>
                        <div class="cy-info-val">{{ $city->best_time_detail }}</div>
                    </div>
                </div>
                @endif
                @if($city->budget_range ?? null)
                <div class="cy-info-item">
                    <div class="cy-info-icon"><i class="fas fa-wallet" aria-hidden="true"></i></div>
                    <div>
                        <div class="cy-info-label">Budget indicatif</div>
                        <div class="cy-info-val">{{ $city->budget_range }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Fun facts --}}
        @if(($city->fun_facts ?? null) && count($city->fun_facts) > 0)
        <div class="dt-reveal">
            <div class="cy-section-label">
                <span class="cy-label-bar"></span>
                <span>Culture & histoire</span>
            </div>
            <h2 class="cy-section-title" style="margin-bottom:32px;">Le <em>saviez-vous ?</em></h2>

            <div class="cy-funfacts">
                @foreach($city->fun_facts as $ff)
                <div class="cy-funfact">
                    <div class="cy-funfact-quote" aria-hidden="true">"</div>
                    <p class="cy-funfact-text">{{ $ff['fact'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="cy-cta-box">
                <p class="cy-cta-box-sub">Prêt à découvrir {{ $city->name }} par vous-même ?</p>
                <a href="#cy-offers" class="cy-cta-box-btn">
                    <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                    Voir les expériences
                </a>
            </div>
        </div>
        @endif

    </div>
</section>
@endif


{{-- ════════════════════════════════════════════
     §7 NEARBY CITIES
════════════════════════════════════════════ --}}
@if($nearbyCities->count() > 0)
<section class="cy-nearby">
    <div class="dt-container">

        <div class="cy-nearby-head dt-reveal">
            <div>
                <div class="cy-section-label">
                    <span class="cy-label-bar"></span>
                    <span>À explorer aussi</span>
                </div>
                <h2 class="cy-section-title">Destinations <em>proches</em></h2>
            </div>
            <a href="{{ route('destinations') }}" class="cy-nearby-all">
                Toutes les destinations <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="cy-nearby-grid">
            @foreach($nearbyCities as $i => $nearby)
            @php
                $nbGrad  = $destGrads[$nearby->name] ?? '160deg,#1F6B44,#0D3822';
                $nbEmoji = $categoryEmojis[$nearby->category ?? 'urban'] ?? '📍';
            @endphp

            <a href="{{ route('destinations.city', $nearby->slug) }}"
               class="cy-nearby-card dt-reveal"
               style="--delay:{{ $i * 0.08 }}s">

                <div class="cy-nearby-img">
                    @if($nearby->cover_image)
                        <img src="{{ mediaUrl($nearby->cover_image) }}"
                             alt="{{ $nearby->name }}, Bénin" loading="lazy">
                    @else
                        <div class="cy-nearby-ph" style="background:linear-gradient({{ $nbGrad }})">
                            <span>{{ $nbEmoji }}</span>
                        </div>
                    @endif
                    <div class="cy-nearby-reveal"></div>
                    <div class="cy-nearby-ov"></div>
                </div>

                @if($nearby->offers_min_base_price)
                <div class="cy-nearby-price">
                    dès {{ number_format($nearby->offers_min_base_price, 0, ',', ' ') }} FCFA
                </div>
                @endif

                <div class="cy-nearby-content">
                    <h3 class="cy-nearby-name">{{ $nearby->name }}</h3>
                    <p class="cy-nearby-meta">
                        {{ $nearby->offers_count }} expérience{{ $nearby->offers_count > 1 ? 's' : '' }}
                        @if($nearby->distance_from_cotonou ?? null) · {{ $nearby->distance_from_cotonou }} @endif
                    </p>
                    <span class="cy-nearby-cta">
                        Découvrir <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </span>
                </div>

            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
(function () {

    /* ── Reveal au scroll ── */
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const delay = e.target.style.getPropertyValue('--delay') || '0s';
                e.target.style.transitionDelay = delay;
                e.target.classList.add('visible');
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.08 });
    document.querySelectorAll('.dt-reveal').forEach(el => obs.observe(el));

    /* ── Meta bar sticky shadow + CTA ── */
    const metaBar = document.getElementById('cy-meta-bar');
    const heroCta = document.querySelector('.cy-meta-cta');
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY > 200;
        metaBar?.classList.toggle('cy-meta-bar--scrolled', scrolled);
        if (heroCta) {
            heroCta.style.opacity      = scrolled ? '1' : '0';
            heroCta.style.pointerEvents = scrolled ? 'auto' : 'none';
        }
    }, { passive: true });

    /* ── Smooth scroll vers les offres ── */
    document.querySelectorAll('a[href="#cy-offers"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('cy-offers')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

})();
</script>
@endpush