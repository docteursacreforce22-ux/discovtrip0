@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/offers/show.css')
@endpush

@section('title', $offer->title . ' — DiscovTrip Bénin')
@section('description', Str::limit(strip_tags($offer->description), 155))
@section('og_title', $offer->title . ' — DiscovTrip')
@section('og_description', Str::limit(strip_tags($offer->description), 155))
@section('og_image', $offer->cover_image ? mediaUrl($offer->cover_image) : asset('images/og-default.jpg'))

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "TouristAttraction",
            "name": "{{ addslashes($offer->title) }}",
            "description": "{{ addslashes(Str::limit(strip_tags($offer->description), 200)) }}",
            "url": "{{ route('offers.show', $offer->slug) }}",
            "image": "{{ $offer->cover_image ? mediaUrl($offer->cover_image) : asset('images/og-default.jpg') }}",
            "touristType": "{{ $offer->category_label ?? 'Culturel' }}",
            "address": {
                "@@type": "PostalAddress",
                "addressLocality": "{{ $offer->city->name ?? 'Bénin' }}",
                "addressCountry": "BJ"
            },
            "offers": {
                "@@type": "Offer",
                "price": "{{ $offer->effective_price }}",
                "priceCurrency": "XOF",
                "availability": "https://schema.org/InStock",
                "url": "{{ route('offers.show', $offer->slug) }}",
                "seller": {
                    "@@type": "Organization",
                    "name": "DiscovTrip"
                }
            }
            @if($offer->average_rating > 0 && ($offer->reviews_count ?? 0) >= 1)
            ,"aggregateRating": {
                "@@type": "AggregateRating",
                "ratingValue": "{{ number_format($offer->average_rating, 1) }}",
                "reviewCount": "{{ $offer->reviews_count }}",
                "bestRating": "5",
                "worstRating": "1"
            }
            @endif
            @if($offer->duration_hours ?? null)
            ,"duration": "PT{{ $offer->duration_hours }}H"
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
                    "name": "Expériences",
                    "item": "{{ route('offers.index') }}"
                },
                @if($offer->city)
                {
                    "@@type": "ListItem",
                    "position": 3,
                    "name": "{{ $offer->city->name }}",
                    "item": "{{ route('destinations.city', $offer->city->slug) }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 4,
                    "name": "{{ addslashes($offer->title) }}",
                    "item": "{{ route('offers.show', $offer->slug) }}"
                }
                @else
                {
                    "@@type": "ListItem",
                    "position": 3,
                    "name": "{{ addslashes($offer->title) }}",
                    "item": "{{ route('offers.show', $offer->slug) }}"
                }
                @endif
            ]
        }
    ]
}
</script>
@endpush

@section('content')

@php
    // ── Normaliser TOUS les champs JSON/array ───────────────────────
    $toArr = fn($v): array => is_array($v)
        ? $v
        : (is_string($v) && $v !== '' ? (json_decode($v, true) ?? []) : []);

    $gallery          = $toArr($offer->gallery);
    $includedOffer    = $toArr($offer->included_items);
    $excludedOffer    = $toArr($offer->excluded_items);
    $offerFaq         = $toArr($offer->faq);
    $offerLanguages   = $toArr($offer->languages);

    // Images disponibles (cover + galerie, dédupliqués)
    $allImages = collect();
    if ($offer->cover_image) {
        $allImages->push($offer->cover_image);
    }
    foreach ($gallery as $img) {
        if ($img && $img !== $offer->cover_image) {
            $allImages->push($img);
        }
    }
    $mainImage = $allImages->first();

    // Tiers actifs
    $tiers     = $offer->activeTiers;
    $firstTier = $tiers->first();
    $initPrice = $firstTier ? (float) $firstTier->price : (float) $offer->effective_price;
    $initWa    = $firstTier ? (bool) $firstTier->whatsapp_only : false;
@endphp

{{-- ══ NAV ANCRES ══════════════════════════════════════ --}}
<nav class="osd-anchors" aria-label="Sections">
    <div class="osd-anchors__inner">
        <a href="#description" class="osd-anchors__link">Description</a>
        @if($tiers->isNotEmpty())
            <a href="#niveaux" class="osd-anchors__link">Niveaux</a>
        @endif
        @if(count($includedOffer) > 0 || count($excludedOffer) > 0)
            <a href="#inclusions" class="osd-anchors__link">Inclusions</a>
        @endif
        <a href="#guide"  class="osd-anchors__link">Guide</a>
        <a href="#faq"    class="osd-anchors__link">FAQ</a>
        <a href="#avis"   class="osd-anchors__link">Avis</a>
        @if($similarOffers->isNotEmpty())
            <a href="#similaires" class="osd-anchors__link">Similaires</a>
        @endif
    </div>
</nav>

{{-- ══ HERO ════════════════════════════════════════════ --}}
<section class="osd-hero">
    <div class="osd-hero__inner">

        {{-- Breadcrumb --}}
        <nav class="osd-breadcrumb" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}">Accueil</a>
            <span class="osd-breadcrumb__sep">›</span>
            <a href="{{ route('offers.index') }}">Expériences</a>
            <span class="osd-breadcrumb__sep">›</span>
            <span>{{ $offer->title }}</span>
        </nav>

        {{-- Badge promo --}}
        @if($offer->is_promo)
            <div class="osd-promo-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Promo −{{ $offer->promo_discount }}%
            </div>
        @endif

        {{-- Titre --}}
        <h1 class="osd-title">{{ $offer->title }}</h1>

        {{-- Pills méta --}}
        <div class="osd-meta">
            <span class="osd-meta__pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $offer->city->name }}{{ $offer->city->country ? ', ' . $offer->city->country->name : '' }}
            </span>
            <span class="osd-meta__pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                {{ $offer->duration_formatted }}
            </span>
            <span class="osd-meta__pill">
                {{ $offer->category_emoji }} {{ $offer->category_label }}
            </span>
            <span class="osd-meta__pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                {{ $offer->min_participants }}–{{ $offer->max_participants }} pers.
            </span>
            @if($offer->difficulty_level)
                <span class="osd-meta__pill">
                    @switch($offer->difficulty_level)
                        @case('easy')        🟢 Facile       @break
                        @case('moderate')    🟡 Modéré       @break
                        @case('challenging') 🟠 Difficile    @break
                        @case('expert')      🔴 Expert       @break
                        @default             {{ $offer->difficulty_level }}
                    @endswitch
                </span>
            @endif
            @if($offer->is_instant_booking)
                <span class="osd-meta__pill" style="color:var(--f-700);border-color:var(--f-400);">
                    ⚡ Réservation instantanée
                </span>
            @endif
            @if(($offer->reviews_count ?? 0) > 0)
                @php $avgMeta = round($offer->reviews->avg('rating'), 1); @endphp
                <span class="osd-meta__pill">
                    ⭐ {{ $avgMeta }} ({{ $offer->reviews->count() }} avis)
                </span>
            @endif
        </div>

    </div>
</section>

{{-- ══ MEDIA (galerie + vidéo) ══════════════════════════ --}}
<div class="osd-media" style="padding-bottom:0;">

    {{-- Onglets si les deux existent --}}
    @if($offer->has_video && $allImages->isNotEmpty())
        <div class="osd-media-tabs">
            <div class="osd-media-tab --active" id="osd-tab-photos" onclick="osdTabSwitch('photos')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                Photos ({{ $allImages->count() }})
            </div>
            <div class="osd-media-tab" id="osd-tab-video" onclick="osdTabSwitch('video')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                Vidéo
            </div>
        </div>
    @endif

    {{-- Panel Photos --}}
    <div id="osd-panel-photos" class="osd-panel">
        @if($mainImage)
            <div class="osd-gallery-main" id="osd-gallery-main" onclick="osdOpenLightbox(0)">
                <img
                    src="{{ Storage::url($mainImage) }}"
                    alt="{{ $offer->title }}"
                    id="osd-main-img"
                >
                <div class="osd-gallery-main__overlay"></div>
                @if($allImages->count() > 1)
                    <button
                        class="osd-gallery-main__cta"
                        onclick="event.stopPropagation(); osdOpenLightbox(0)"
                        type="button"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        Voir {{ $allImages->count() }} photos
                    </button>
                @endif
            </div>

            {{-- Strip miniatures en dessous --}}
            @if($allImages->count() > 1)
                <div class="osd-strip" id="osd-strip">
                    @foreach($allImages as $i => $img)
                        <div
                            class="osd-strip__item {{ $i === 0 ? '--active' : '' }}"
                            onclick="osdSetMain({{ $i }}, this)"
                        >
                            <img
                                src="{{ Storage::url($img) }}"
                                alt="Photo {{ $i + 1 }}"
                                loading="lazy"
                            >
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            <div class="osd-gallery-main__placeholder">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            </div>
        @endif
    </div>

    {{-- Panel Vidéo --}}
    @if($offer->has_video)
        <div
            id="osd-panel-video"
            class="osd-panel {{ $allImages->isNotEmpty() ? '--hidden' : '' }}"
        >
            <div class="osd-video-panel">
                <iframe
                    src="{{ $offer->video_embed_url }}"
                    title="{{ $offer->title }} — Vidéo"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        </div>
    @endif

</div>{{-- fin .osd-media --}}

{{-- ══ LAYOUT PRINCIPAL ════════════════════════════════ --}}
<div class="osd-layout">

    {{-- ━━ COLONNE GAUCHE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div>

        {{-- ── DESCRIPTION ──────────────────────────── --}}
        <section class="osd-section" id="description">
            <h2 class="osd-section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                L'expérience
            </h2>
            <div class="osd-description">
                @if($offer->long_description)
                    {!! strip_tags($offer->long_description, '<p><br><strong><em><ul><ol><li><h2><h3><h4><blockquote><a>') !!}
                @else
                    <p>{{ $offer->description }}</p>
                @endif
            </div>
        </section>

        {{-- ── NIVEAUX (tiers détaillés) ────────────── --}}
        @if($tiers->isNotEmpty())
            <section class="osd-section" id="niveaux">
                <h2 class="osd-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Choisissez votre niveau
                </h2>
                <div class="osd-tier-cards">
                    @foreach($tiers as $tier)
                        @php
                            $tierIncluded = is_array($tier->included_items)
                                ? $tier->included_items
                                : (is_string($tier->included_items) ? (json_decode($tier->included_items, true) ?? []) : []);
                        @endphp
                        <div class="osd-tier-card osd-tier-card--{{ $tier->type ?? 'discovery' }}">

                            {{-- Badge "Recommandé" sur confort --}}
                            @if(($tier->type ?? '') === 'comfort')
                                <div class="osd-tc-popular">Recommandé</div>
                            @endif

                            {{-- Bande colorée --}}
                            <div class="osd-tc-band">
                                <span class="osd-tc-emoji">{{ $tier->emoji }}</span>
                                <div>
                                    <div class="osd-tc-band-label">{{ $tier->label }}</div>
                                    @if($tier->tagline)
                                        <div class="osd-tc-band-tagline">{{ $tier->tagline }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Corps --}}
                            <div class="osd-tc-body">

                                {{-- Prix --}}
                                <div class="osd-tc-price">
                                    {{ $tier->price_formatted }}
                                    <span>{{ $tier->price_eur }}</span>
                                </div>

                                {{-- Description --}}
                                @if($tier->description)
                                    <p class="osd-tc-desc">{{ $tier->description }}</p>
                                @endif

                                {{-- Items inclus --}}
                                @if(count($tierIncluded) > 0)
                                    <ul class="osd-tc-items">
                                        @foreach($tierIncluded as $item)
                                            <li>
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                                                {{ is_array($item) ? ($item['item'] ?? '') : $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                {{-- Note WhatsApp only --}}
                                @if($tier->whatsapp_only)
                                    <div class="osd-tc-wa-note">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        Finalisation via WhatsApp
                                    </div>
                                @endif

                            </div>{{-- fin osd-tc-body --}}

                            {{-- Bouton sélection --}}
                            <div class="osd-tc-footer">
                                <button
                                    class="osd-tc-select-btn"
                                    onclick="osdSelectFromCard({{ $tier->id }}, {{ $tier->price }}, {{ $tier->whatsapp_only ? 'true' : 'false' }})"
                                    type="button"
                                >
                                    Sélectionner ce niveau
                                </button>
                            </div>

                        </div>{{-- fin osd-tier-card --}}
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ── INCLUSIONS ───────────────────────────── --}}
        @if(count($includedOffer) > 0 || count($excludedOffer) > 0)
            <section class="osd-section" id="inclusions">
                <h2 class="osd-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Ce qui est inclus
                </h2>
                <div class="osd-inclusions">
                    @if(count($includedOffer) > 0)
                        <div class="osd-incl-block --yes">
                            <h4>✅ Inclus</h4>
                            <ul class="osd-incl-list">
                                @foreach($includedOffer as $item)
                                    <li>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                                        {{ is_array($item) ? ($item['item'] ?? '') : $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(count($excludedOffer) > 0)
                        <div class="osd-incl-block --no">
                            <h4>❌ Non inclus</h4>
                            <ul class="osd-incl-list">
                                @foreach($excludedOffer as $item)
                                    <li>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        {{ is_array($item) ? ($item['item'] ?? '') : $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- ── GUIDE ────────────────────────────────── --}}
        <section class="osd-section" id="guide">
            <h2 class="osd-section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Votre guide
            </h2>

            @if($offer->show_guide_profile && $offer->user)
                <div class="osd-guide-card">
                    @php
                        $avatarPath = $offer->user->avatar ?? $offer->user->profile_photo_path ?? null;
                    @endphp
                    @if($avatarPath)
                        <img src="{{ Storage::url($avatarPath) }}" alt="{{ $offer->user->first_name }}" class="osd-guide-avatar">
                    @else
                        <div class="osd-guide-avatar-ph">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                    @endif
                    <div>
                        <div class="osd-guide-name">{{ $offer->user->first_name }} {{ $offer->user->last_name }}</div>
                        <div class="osd-guide-role">Guide certifié DiscovTrip</div>
                        <p class="osd-guide-desc">{{ $offer->guide_type_description }}</p>
                    </div>
                </div>
            @elseif(($offer->guide_type ?? 'agency') === 'on_site')
                <div class="osd-guide-generic">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <div>
                        <div class="osd-guide-generic__title">Guide local officiel du site</div>
                        <p class="osd-guide-generic__desc">{{ $offer->guide_type_description }}</p>
                    </div>
                </div>
            @else
                <div class="osd-guide-generic">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    <div>
                        <div class="osd-guide-generic__title">Guide certifié DiscovTrip</div>
                        <p class="osd-guide-generic__desc">{{ $offer->guide_type_description }}</p>
                    </div>
                </div>
            @endif

            {{-- Langues --}}
            @if(count($offerLanguages) > 0)
                <div class="osd-langs">
                    @foreach($offerLanguages as $lang)
                        <span class="osd-lang-tag">🌐 {{ $lang }}</span>
                    @endforeach
                </div>
            @endif

            {{-- Point de RDV --}}
            @if($offer->meeting_point)
                <div class="osd-meeting">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span><strong>Point de départ :</strong> {{ $offer->meeting_point }}</span>
                </div>
            @endif
        </section>

        {{-- ── FAQ ──────────────────────────────────── --}}
        <section class="osd-section" id="faq">
            <h2 class="osd-section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Questions fréquentes
            </h2>
            @php
                $faqs = count($offerFaq) > 0 ? $offerFaq : [
                    ['q' => 'Que faut-il apporter ?',                      'r' => 'Tenues légères et confortables, protection solaire, eau en quantité suffisante et un appareil photo pour immortaliser vos souvenirs.'],
                    ['q' => 'Quelle est la politique d\'annulation ?',      'r' => 'Annulation gratuite jusqu\'à 48h avant le départ. Au-delà, des frais peuvent s\'appliquer selon les conditions de l\'expérience.'],
                    ['q' => 'L\'expérience est-elle accessible aux enfants ?', 'r' => $offer->min_age ? 'Cette expérience est réservée aux participants de ' . $offer->min_age . ' ans et plus.' : 'Oui, cette expérience est ouverte à tous les âges. Contactez-nous pour les conditions spécifiques aux enfants.'],
                    ['q' => 'Que se passe-t-il en cas de mauvais temps ?', 'r' => 'En cas de conditions défavorables, nous vous contacterons pour reprogrammer ou annuler avec remboursement intégral.'],
                ];
            @endphp
            @foreach($faqs as $faqIndex => $faq)
                <div class="osd-faq-item" id="faq-{{ $faqIndex }}">
                    <button class="osd-faq-trigger" onclick="osdToggleFaq({{ $faqIndex }})" type="button">
                        <span>{{ $faq['q'] ?? '' }}</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="osd-faq-body">{{ $faq['r'] ?? '' }}</div>
                </div>
            @endforeach
        </section>

        {{-- ── AVIS ─────────────────────────────────── --}}
        <section class="osd-section" id="avis">
            <h2 class="osd-section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Avis des voyageurs
            </h2>
            @if(($offer->reviews_count ?? 0) > 0)
                @php $avgRating = round((float)($offer->reviews_avg_rating ?? 0), 1); @endphp
                <div class="osd-rating-bar">
                    <div class="osd-rating-big">{{ $avgRating }}</div>
                    <div>
                        <div class="osd-stars" aria-label="{{ $avgRating }} étoiles sur 5">
                            @php
                                $starF17 = '<svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                $starE17 = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                $fc17 = min((int)floor($avgRating), 5);
                                echo str_repeat($starF17, $fc17) . str_repeat($starE17, 5 - $fc17);
                            @endphp
                        </div>
                        <div class="osd-review-count">{{ $offer->reviews_count }} avis vérifiés</div>
                    </div>
                </div>
                <div class="osd-reviews-grid">
                    @foreach($offer->reviews->take(4) as $review)
                        <div class="osd-review-card">
                            <div class="osd-review-header">
                                <div class="osd-reviewer-avatar">
                                    {{ strtoupper(substr($review->user?->name ?? 'V', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="osd-reviewer-name">{{ $review->user?->name ?? 'Voyageur' }}</div>
                                    <div class="osd-review-date">{{ $review->created_at->translatedFormat('F Y') }}</div>
                                </div>
                            </div>
                            <div class="osd-stars" style="margin-bottom:.5rem;" aria-label="Note {{ $review->rating }} sur 5">
                                @php
                                    $starF12 = '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                    $starE12 = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                    $fc12 = min((int)$review->rating, 5);
                                    echo str_repeat($starF12, $fc12) . str_repeat($starE12, 5 - $fc12);
                                @endphp
                            </div>
                            <p class="osd-review-text">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="osd-review-empty">
                    <p>Aucun avis pour l'instant.</p>
                    <p style="margin-top:.3rem;font-size:.78rem;">Soyez le premier à partager votre expérience !</p>
                </div>
            @endif
        </section>

    </div>{{-- fin colonne gauche --}}

    {{-- ━━ BOOKING CARD (droite) ━━━━━━━━━━━━━━━━━━━━━━ --}}
    <aside>
        <div class="osd-booking-card">

            {{-- ① Barre urgence — TOUJOURS EN PREMIER --}}
            @if($offer->available_spots !== null && $offer->available_spots > 0 && $offer->available_spots <= 5)
                <div class="osd-urgency">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Plus que {{ $offer->available_spots }} place{{ $offer->available_spots > 1 ? 's' : '' }} !
                </div>
            @elseif($offer->is_promo && $offer->promotion_ends_at)
                <div class="osd-urgency">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    Promo expire le {{ $offer->promotion_ends_at->format('d/m/Y') }}
                </div>
            @endif

            <div class="osd-booking-inner">

                {{-- ② Prix --}}
                <div class="osd-price-block">
                    @if($offer->is_promo)
                        <div class="osd-price-original">{{ number_format($offer->base_price, 0, ',', ' ') }} FCFA</div>
                        <div class="osd-price-main --promo" id="osd-display-price">{{ number_format($offer->promotional_price, 0, ',', ' ') }} FCFA</div>
                        <div class="osd-promo-save">🎉 Économisez {{ number_format($offer->base_price - $offer->promotional_price, 0, ',', ' ') }} FCFA</div>
                    @else
                        <div class="osd-price-main" id="osd-display-price">{{ number_format($initPrice, 0, ',', ' ') }} FCFA</div>
                    @endif
                    <div class="osd-price-per">par personne</div>
                </div>

                {{-- ③ Sélecteur de tiers compact --}}
                @if($tiers->isNotEmpty())
                    <div class="osd-tiers" id="osd-tier-selector">
                        @foreach($tiers as $tier)
                            <div
                                class="osd-tier-select {{ $loop->first ? '--selected' : '' }}"
                                id="osd-tier-select-{{ $tier->id }}"
                                data-tier-id="{{ $tier->id }}"
                                data-tier-price="{{ $tier->price }}"
                                data-tier-label="{{ $tier->label }}"
                                data-tier-wa="{{ $tier->whatsapp_only ? '1' : '0' }}"
                                data-tier-indicative="{{ $tier->price_is_indicative ? '1' : '0' }}"
                                onclick="osdSelectTier(this)"
                            >
                                <div class="osd-tier-select__left">
                                    <div class="osd-tier-select__name-row">
                                        <span class="osd-tier-select__emoji">{{ $tier->emoji }}</span>
                                        <span class="osd-tier-select__name">{{ $tier->label }}</span>
                                        @if(($tier->type ?? '') === 'exception')
                                            <span class="osd-tier-select__tag osd-tier-select__tag--exception">VIP</span>
                                        @elseif(($tier->type ?? '') === 'comfort')
                                            <span class="osd-tier-select__tag osd-tier-select__tag--comfort">✦</span>
                                        @endif
                                    </div>
                                    @if($tier->tagline)
                                        <div class="osd-tier-select__tagline">{{ $tier->tagline }}</div>
                                    @endif
                                </div>
                                <div class="osd-tier-select__right">
                                    <div class="osd-tier-select__price">
                                        {{ $tier->price_is_indicative ? 'dès ' : '' }}{{ number_format($tier->price, 0, ',', ' ') }} FCFA
                                    </div>
                                    @if($tier->whatsapp_only)
                                        <div class="osd-tier-select__indicative">Via WhatsApp</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- ④ Participants --}}
                <div class="osd-participants">
                    <span class="osd-participants__label">Participants</span>
                    <div class="osd-participants__ctrl">
                        <button class="osd-participants__btn" id="osd-btn-minus" onclick="osdChangePax(-1)" type="button">−</button>
                        <span class="osd-participants__count" id="osd-pax-count">{{ $offer->min_participants ?? 1 }}</span>
                        <button class="osd-participants__btn" id="osd-btn-plus"  onclick="osdChangePax(1)"  type="button">+</button>
                    </div>
                </div>

                {{-- ⑤ Récapitulatif --}}
                <div class="osd-recap">
                    @if($tiers->isNotEmpty())
                        <div class="osd-recap__tier">
                            <span>Niveau sélectionné</span>
                            <span id="osd-recap-tier-label">{{ $firstTier->label }}</span>
                        </div>
                    @endif
                    <div class="osd-recap__row">
                        <span>Prix unitaire</span>
                        <span id="osd-recap-unit">{{ number_format($initPrice, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="osd-recap__row">
                        <span>Participants</span>
                        <span id="osd-recap-pax">× {{ $offer->min_participants ?? 1 }}</span>
                    </div>
                    <div class="osd-recap__total">
                        <span>Total estimé</span>
                        <span id="osd-recap-total">{{ number_format($initPrice * ($offer->min_participants ?? 1), 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                {{-- ⑥ CTA -- standard (masqué si WhatsApp only) --}}
                <div id="osd-cta-standard" class="{{ $initWa ? '--hidden' : '' }}">
                    @if($hasOnSite && !$hasOnline)
                        <a href="{{ route('bookings.create', $offer->slug) }}" class="osd-btn-book --primary">
                            Réserver cette expérience
                        </a>
                    @elseif($hasOnline && !$hasOnSite)
                        <a href="{{ route('bookings.create', ['slug' => $offer->slug, 'payment' => 'online']) }}" class="osd-btn-book --online">
                            💳 Payer en ligne
                        </a>
                    @elseif($hasOnline && $hasOnSite)
                        <a href="{{ route('bookings.create', ['slug' => $offer->slug, 'payment' => 'online']) }}" class="osd-btn-book --online">
                            💳 Payer en ligne maintenant
                        </a>
                        <a href="{{ route('bookings.create', $offer->slug) }}" class="osd-btn-book --outline">
                            📅 Réserver · payer sur place
                        </a>
                    @else
                        <a href="{{ route('bookings.create', $offer->slug) }}" class="osd-btn-book --primary">
                            Réserver cette expérience
                        </a>
                    @endif
                </div>

                {{-- WhatsApp --}}
                @php
                    $waMsg   = urlencode('Bonjour DiscovTrip ! Je suis intéressé par l\'expérience "' . $offer->title . '" (' . $offer->city->name . '). Pouvez-vous me renseigner ?');
                    $waPhone = config('discovtrip.whatsapp_phone_raw', '22901000000');
                @endphp
                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" id="osd-btn-wa" class="osd-btn-book --whatsapp {{ $initWa ? '' : '' }}" target="_blank" rel="noopener">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="display:inline;vertical-align:middle;margin-right:.3rem;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    {{ $initWa ? 'Réserver via WhatsApp' : 'Demander par WhatsApp' }}
                </a>

                <div class="osd-secure">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Réservation sécurisée · Annulation flexible
                </div>

            </div>{{-- fin booking-inner --}}
        </div>{{-- fin booking-card --}}
    </aside>

</div>{{-- fin osd-layout --}}

{{-- ══ OFFRES SIMILAIRES ══════════════════════════════ --}}
@if($similarOffers->isNotEmpty())
    <section class="osd-similar" id="similaires">
        <div class="osd-similar__inner">
            <h2 class="osd-similar__title">Vous aimerez aussi</h2>
            <div class="osd-similar-grid">
                @foreach($similarOffers as $sim)
                    <a href="{{ route('offers.show', $sim->slug) }}" class="osd-sim-card">
                        @if($sim->cover_image)
                            <img src="{{ Storage::url($sim->cover_image) }}" alt="{{ $sim->title }}" class="osd-sim-img" loading="lazy">
                        @else
                            <div class="osd-sim-img-ph">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            </div>
                        @endif
                        <div class="osd-sim-body">
                            <div class="osd-sim-name">{{ $sim->title }}</div>
                            <div class="osd-sim-city">{{ $sim->city->name }}</div>
                            <div class="osd-sim-price">
                                @if($sim->is_promo)
                                    <span style="text-decoration:line-through;color:var(--tx-muted);font-weight:400;font-size:.75rem;">{{ number_format($sim->base_price, 0, ',', ' ') }}</span>
                                    {{ number_format($sim->promotional_price, 0, ',', ' ') }} FCFA
                                @else
                                    {{ number_format($sim->effective_price, 0, ',', ' ') }} FCFA
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ══ LIGHTBOX ════════════════════════════════════════ --}}
@php
    $lightboxImages = collect();
    if ($offer->cover_image) {
        $lightboxImages->push($offer->cover_image);
    }
    foreach ($gallery as $img) {
        if ($img && $img !== $offer->cover_image) {
            $lightboxImages->push($img);
        }
    }
@endphp

@if($lightboxImages->count() > 1)
    <div class="osd-lightbox" id="osd-lightbox" role="dialog" aria-modal="true" aria-label="Galerie photos">
        <div class="osd-lightbox__overlay" onclick="osdCloseLightbox()"></div>
        <div class="osd-lightbox__inner">
            <button class="osd-lightbox__close" onclick="osdCloseLightbox()" type="button" aria-label="Fermer">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <button class="osd-lightbox__nav osd-lightbox__prev" onclick="osdLightboxNav(-1)" type="button" aria-label="Précédent">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <img src="" alt="" class="osd-lightbox__img" id="osd-lightbox-img">
            <button class="osd-lightbox__nav osd-lightbox__next" onclick="osdLightboxNav(1)" type="button" aria-label="Suivant">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
            </button>
            <div class="osd-lightbox__counter" id="osd-lightbox-counter">1 / {{ $lightboxImages->count() }}</div>
        </div>
    </div>
    <script>
        window._osdImages = @json($lightboxImages->map(fn($img) => Storage::url($img))->values());
    </script>
@endif

@endsection

{{-- ══ SCRIPTS ═════════════════════════════════════════ --}}
{{-- ══ BARRE STICKY MOBILE — visible uniquement < 1024px ══ --}}
{{-- Affiche le prix + bouton Réserver en bas d'écran sur mobile --}}
<div id="osd-mobile-bar" aria-hidden="true" style="
    display:none;
    position:fixed;bottom:0;left:0;right:0;z-index:800;
    background:#fff;
    border-top:1px solid rgba(28,27,22,.1);
    padding:12px 20px 16px;
    padding-bottom:calc(12px + env(safe-area-inset-bottom));
    box-shadow:0 -4px 24px rgba(0,0,0,.12);
    align-items:center;gap:12px;
">
    <div style="flex:1;min-width:0">
        <div style="font-size:11px;color:#7A7868;text-transform:uppercase;letter-spacing:.06em">À partir de</div>
        <div style="font-size:18px;font-weight:700;color:#0D3822;line-height:1.2">
            {{ number_format($offer->effective_price, 0, ',', ' ') }}
            <span style="font-size:13px;font-weight:500">FCFA</span>
        </div>
    </div>
    <a href="{{ route('bookings.create', $offer->slug) }}"
       style="
           flex-shrink:0;
           display:inline-flex;align-items:center;gap:8px;
           padding:13px 22px;border-radius:100px;
           background:#D4A20F;color:#1A1A17;
           font-weight:700;font-size:14px;
           text-decoration:none;
           box-shadow:0 4px 16px rgba(212,162,15,.4);
           white-space:nowrap;
       ">
        <i class="fas fa-calendar-check" aria-hidden="true"></i>
        Réserver
    </a>
</div>
<style>
@media (max-width: 1023px) {
    #osd-mobile-bar { display: flex !important; }
    /* Espace en bas pour éviter que la barre cache le contenu */
    .osd-page { padding-bottom: 90px; }
}
</style>

@push('scripts')
<script>
(function () {
    'use strict';



    // ── État ────────────────────────────────────────────────
    var _basePrice = {{ (float) $initPrice }};
    var _minPax    = {{ (int) ($offer->min_participants ?? 1) }};
    var _maxPax    = {{ (int) ($offer->max_participants ?? 20) }};
    var _pax       = _minPax;
    var _tierPrice = _basePrice;
    var _tierLabel = '{{ addslashes($firstTier ? $firstTier->label : '') }}';
    var _isWa      = {{ $initWa ? 'true' : 'false' }};
    var _lbIndex   = 0;

    // ── Onglets média ───────────────────────────────────────
    window.osdTabSwitch = function (tab) {
        var photos = document.getElementById('osd-panel-photos');
        var video  = document.getElementById('osd-panel-video');
        var tPh    = document.getElementById('osd-tab-photos');
        var tVid   = document.getElementById('osd-tab-video');
        if (!photos || !video) return;
        if (tab === 'photos') {
            photos.classList.remove('--hidden');
            video.classList.add('--hidden');
            if (tPh)  tPh.classList.add('--active');
            if (tVid) tVid.classList.remove('--active');
        } else {
            photos.classList.add('--hidden');
            video.classList.remove('--hidden');
            if (tPh)  tPh.classList.remove('--active');
            if (tVid) tVid.classList.add('--active');
        }
    };

    // ── Galerie strip ───────────────────────────────────────
    window.osdSetMain = function (index, thumbEl) {
        var imgs   = window._osdImages || [];
        var mainEl = document.getElementById('osd-main-img');
        if (mainEl && imgs[index]) mainEl.src = imgs[index];
        document.querySelectorAll('.osd-strip__item').forEach(function (t) {
            t.classList.remove('--active');
        });
        if (thumbEl) thumbEl.classList.add('--active');
        _lbIndex = index;
    };

    // ── Sélection tier (depuis booking card compact) ────────
    window.osdSelectTier = function (el) {
        document.querySelectorAll('.osd-tier-select').forEach(function (t) {
            t.classList.remove('--selected');
        });
        el.classList.add('--selected');
        _tierPrice = parseFloat(el.dataset.tierPrice) || _basePrice;
        _tierLabel = el.dataset.tierLabel || '';
        _isWa      = el.dataset.tierWa === '1';
        osdUpdateRecap();
        osdUpdateCtaVisibility();
    };

    // ── Sélection tier (depuis cartes détaillées) ───────────
    window.osdSelectFromCard = function (tierId, price, isWa) {
        var el = document.querySelector('[data-tier-id="' + tierId + '"]');
        if (el) {
            osdSelectTier(el);
        } else {
            _tierPrice = parseFloat(price) || _basePrice;
            _isWa      = !!isWa;
            osdUpdateRecap();
            osdUpdateCtaVisibility();
        }
        // Scroll vers la booking card
        var card = document.querySelector('.osd-booking-card');
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    // ── CTA visibility selon WhatsApp only ─────────────────
    function osdUpdateCtaVisibility() {
        var std  = document.getElementById('osd-cta-standard');
        var waBtn = document.getElementById('osd-btn-wa');
        if (!std) return;
        if (_isWa) {
            std.classList.add('--hidden');
            if (waBtn) waBtn.textContent = '🟢 Réserver via WhatsApp';
        } else {
            std.classList.remove('--hidden');
            if (waBtn) waBtn.innerHTML = waBtn.innerHTML.replace('Réserver via WhatsApp', 'Demander par WhatsApp');
        }
    }

    // ── Participants ────────────────────────────────────────
    window.osdChangePax = function (delta) {
        var next = _pax + delta;
        if (next < _minPax || next > _maxPax) return;
        _pax = next;
        var countEl = document.getElementById('osd-pax-count');
        var minusEl = document.getElementById('osd-btn-minus');
        var plusEl  = document.getElementById('osd-btn-plus');
        if (countEl) countEl.textContent = _pax;
        if (minusEl) minusEl.disabled = (_pax <= _minPax);
        if (plusEl)  plusEl.disabled  = (_pax >= _maxPax);
        osdUpdateRecap();
    };

    function osdUpdateRecap() {
        var total = _tierPrice * _pax;
        var fmt   = function (n) { return n.toLocaleString('fr-FR') + ' FCFA'; };
        var unitEl  = document.getElementById('osd-recap-unit');
        var paxEl   = document.getElementById('osd-recap-pax');
        var totEl   = document.getElementById('osd-recap-total');
        var priceEl = document.getElementById('osd-display-price');
        var lblEl   = document.getElementById('osd-recap-tier-label');
        if (unitEl)  unitEl.textContent  = fmt(_tierPrice);
        if (paxEl)   paxEl.textContent   = '× ' + _pax;
        if (totEl)   totEl.textContent   = fmt(total);
        if (priceEl) priceEl.textContent = fmt(_tierPrice);
        if (lblEl)   lblEl.textContent   = _tierLabel;
    }

    // ── FAQ ─────────────────────────────────────────────────
    window.osdToggleFaq = function (i) {
        var item = document.getElementById('faq-' + i);
        if (item) item.classList.toggle('--open');
    };

    // ── Lightbox ────────────────────────────────────────────
    window.osdOpenLightbox = function (index) {
        var lb = document.getElementById('osd-lightbox');
        if (!lb) return;
        _lbIndex = index;
        osdLbShow();
        lb.classList.add('--open');
        document.body.style.overflow = 'hidden';
    };
    window.osdCloseLightbox = function () {
        var lb = document.getElementById('osd-lightbox');
        if (!lb) return;
        lb.classList.remove('--open');
        document.body.style.overflow = '';
    };
    window.osdLightboxNav = function (delta) {
        var imgs = window._osdImages || [];
        if (!imgs.length) return;
        _lbIndex = (_lbIndex + delta + imgs.length) % imgs.length;
        osdLbShow();
    };
    function osdLbShow() {
        var imgs  = window._osdImages || [];
        var imgEl = document.getElementById('osd-lightbox-img');
        var ctrEl = document.getElementById('osd-lightbox-counter');
        if (imgEl && imgs[_lbIndex]) imgEl.src = imgs[_lbIndex];
        if (ctrEl) ctrEl.textContent = (_lbIndex + 1) + ' / ' + imgs.length;
    }
    document.addEventListener('keydown', function (e) {
        var lb = document.getElementById('osd-lightbox');
        if (!lb || !lb.classList.contains('--open')) return;
        if (e.key === 'Escape')     osdCloseLightbox();
        if (e.key === 'ArrowLeft')  osdLightboxNav(-1);
        if (e.key === 'ArrowRight') osdLightboxNav(1);
    });

    // ── Nav ancres active ────────────────────────────────────
    var _anchorIds = ['description', 'niveaux', 'inclusions', 'guide', 'faq', 'avis', 'similaires'];
    var _anchorLinks = document.querySelectorAll('.osd-anchors__link');
    window.addEventListener('scroll', function () {
        var scrollY = window.scrollY + 150;
        _anchorIds.forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            if (scrollY >= el.offsetTop && scrollY < el.offsetTop + el.offsetHeight) {
                _anchorLinks.forEach(function (l) { l.classList.remove('--active'); });
                var a = document.querySelector('.osd-anchors__link[href="#' + id + '"]');
                if (a) a.classList.add('--active');
            }
        });
    }, { passive: true });

    // ── Init ─────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var minusEl = document.getElementById('osd-btn-minus');
        var plusEl  = document.getElementById('osd-btn-plus');
        if (minusEl) minusEl.disabled = (_pax <= _minPax);
        if (plusEl)  plusEl.disabled  = (_pax >= _maxPax);
        osdUpdateRecap();
        osdUpdateCtaVisibility();
    });

}());

// Mobile sticky bar — hide when booking card is visible
(function() {
    var mobileBar  = document.getElementById('osd-mobile-bar');
    var bookingCard = document.querySelector('.osd-booking-card');
    if (!mobileBar || !bookingCard || !('IntersectionObserver' in window)) return;

    new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            mobileBar.style.display = e.isIntersecting ? 'none' : '';
        });
    }, { threshold: 0.1 }).observe(bookingCard);
})();
</script>
@endpush