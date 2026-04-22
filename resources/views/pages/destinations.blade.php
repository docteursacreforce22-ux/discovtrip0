@extends('layouts.app')

@section('title', 'Destinations — Explorez le Bénin Authentique | DiscovTrip')

@push('meta')
<meta name="description" content="Découvrez {{ $totalCities }} destinations authentiques au Bénin : Cotonou, Ganvié, Ouidah, Abomey et plus encore. {{ $totalOffers }} expériences uniques avec des guides locaux certifiés.">
<meta property="og:title" content="Destinations — Explorez le Bénin Authentique | DiscovTrip">
<meta property="og:description" content="Découvrez {{ $totalCities }} destinations authentiques au Bénin avec {{ $totalOffers }} expériences uniques.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@endpush

@push('styles')
    @vite('resources/css/pages/destinations.css')
@endpush

@php
$categoryEmojis = ['urban'=>'🏙️','historical'=>'🏛️','nature'=>'🌿','coastal'=>'🏖️'];
$categoryLabels = ['urban'=>'Urbain','historical'=>'Historique','nature'=>'Nature','coastal'=>'Côtière'];
$destGrads = [
    'Cotonou'    =>'160deg,#1F6B44,#0D3822',
    'Ganvié'     =>'160deg,#1a4a6e,#0d2a42',
    'Ouidah'     =>'160deg,#6B1F5A,#3D0D33',
    'Abomey'     =>'160deg,#4a6e1a,#2a420d',
    'Grand-Popo' =>'160deg,#6e4a1a,#42280d',
    'Porto-Novo' =>'160deg,#1a3a6e,#0d1e42',
    'Parakou'    =>'160deg,#1F6B44,#0D3822',
    'Natitingou' =>'160deg,#6e3a1a,#42200d',
];
$categoryCounts = ['all' => $cities->total()];
foreach ($cities as $c) {
    $cat = $c->category ?? 'urban';
    $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
}
@endphp

@section('content')

{{-- ══════════════════════════════════════════════
     §1  HERO — wax sombre + search bar
══════════════════════════════════════════════ --}}
<section class="dp-hero">

    {{-- Fond : image DB ou var(--f-900) + motif wax --}}
    <x-hero-bg setting-key="hero_destinations" pattern-id="wp-dp" />

    <div class="dt-container dp-hero-inner">

        {{-- Texte --}}
        <div class="dp-hero-text">
            <div class="dp-hero-eyebrow dp-anim dp-anim-1">
                <span class="dp-eyebrow-dot"></span>
                Bénin · Afrique de l'Ouest
                <span class="dp-eyebrow-dot"></span>
            </div>
            <h1 class="dp-hero-title dp-anim dp-anim-2">
                Chaque<br>destination,<br><em>une âme.</em>
            </h1>
            <p class="dp-hero-sub dp-anim dp-anim-3">
                Du delta de l'Ouémé aux plateaux de l'Atakora,
                {{ $totalOffers }} expériences uniques avec des guides locaux certifiés.
            </p>

            {{-- Search bar --}}
            <div class="dp-search dp-anim dp-anim-4">
                <i class="fas fa-search dp-search-icon" aria-hidden="true"></i>
                <input type="search"
                       id="dp-search-input"
                       class="dp-search-input"
                       placeholder="Rechercher une destination… Cotonou, Ganvié, Ouidah"
                       aria-label="Rechercher une destination"
                       autocomplete="off">
                <span class="dp-search-clear" id="dp-search-clear" aria-label="Effacer" role="button">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        </div>

        {{-- Stats monumentales --}}
        <div class="dp-hero-stats dp-anim dp-anim-3">
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $totalCities }}">0</span>
                <span class="dp-hstat-lbl">Destinations</span>
            </div>
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $totalOffers }}">0</span>
                <span class="dp-hstat-lbl">Expériences</span>
            </div>
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <span class="dp-hstat-num">{{ number_format($globalRating, 1) }}</span>
                <span class="dp-hstat-lbl">Note / 5</span>
            </div>
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <div class="dp-hstat-row">
                    <span class="dp-hstat-num" data-target="{{ $totalReviews }}">0</span>
                    <span class="dp-hstat-sup">+</span>
                </div>
                <span class="dp-hstat-lbl">Avis vérifiés</span>
            </div>
        </div>
    </div>

    {{-- Vague sombre → sombre (le showcase continue le fond) --}}
    <div class="dp-hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none">
            <path d="M0,36 C360,72 1080,0 1440,36 L1440,72 L0,72 Z" fill="var(--f-900)"/>
        </svg>
    </div>
</section>


{{-- ══════════════════════════════════════════════
     §2  SHOWCASE — fond sombre, image plein format
══════════════════════════════════════════════ --}}
@if($featuredCities->count() > 0)
<section class="dp-showcase" id="dp-showcase">

    <div class="dp-showcase-label dt-container">
        <span class="dp-label-bar-light"></span>
        <span>Destinations à la une</span>
    </div>

    <div class="dp-showcase-track" id="dp-showcase-track">
        @foreach($featuredCities as $i => $city)
        @php
            $grad   = $destGrads[$city->name] ?? '160deg,#1F6B44,#0D3822';
            $rating = $city->average_rating > 0 ? number_format($city->average_rating, 1) : '4.8';
        @endphp

        <div class="dp-slide {{ $i === 0 ? 'dp-slide--active' : '' }}" data-index="{{ $i }}">

            {{-- Image plein format en fond --}}
            <div class="dp-slide-bg">
                @if($city->cover_image)
                    <img src="{{ mediaUrl($city->cover_image) }}"
                         alt="Vue de {{ $city->name }}, Bénin"
                         class="dp-slide-bg-img"
                         loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                @else
                    <div class="dp-slide-bg-ph" style="background:linear-gradient({{ $grad }})"></div>
                @endif
                <div class="dp-slide-bg-overlay"></div>
            </div>

            {{-- Contenu centré --}}
            <div class="dt-container dp-slide-inner">

                <div class="dp-slide-left">
                    <div class="dp-slide-eyebrow">
                        <span>🇧🇯 Bénin</span>
                        @if($city->category)
                            <span class="dp-slide-cat">{{ $categoryLabels[$city->category] ?? '' }}</span>
                        @endif
                    </div>

                    <h2 class="dp-slide-title">{{ $city->name }}</h2>

                    @if($city->description)
                        <p class="dp-slide-desc">{{ Str::limit($city->description, 160) }}</p>
                    @endif

                    <div class="dp-slide-chips">
                        @if($city->offers_count > 0)
                        <span class="dp-slide-chip">
                            <i class="fas fa-ticket-alt"></i>
                            {{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }}
                        </span>
                        @endif
                        @if($city->offers_min_base_price)
                        <span class="dp-slide-chip">
                            <i class="fas fa-tag"></i>
                            À partir de {{ number_format($city->offers_min_base_price, 0, ',', ' ') }} FCFA
                        </span>
                        @endif
                        @if($city->distance_from_cotonou ?? null)
                        <span class="dp-slide-chip">
                            <i class="fas fa-route"></i>
                            {{ $city->distance_from_cotonou }} de Cotonou
                        </span>
                        @endif
                        <span class="dp-slide-chip dp-slide-chip--rating">
                            <i class="fas fa-star"></i>
                            {{ $rating }} / 5
                        </span>
                    </div>

                    <a href="{{ route('destinations.city', $city->slug) }}" class="dp-slide-cta">
                        Découvrir {{ $city->name }}
                        <span class="dp-cta-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>

                {{-- Numéro décoratif --}}
                <div class="dp-slide-num" aria-hidden="true">
                    {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Navigation --}}
    <div class="dp-showcase-nav dt-container">
        <div class="dp-nav-dots" id="dp-nav-dots">
            @foreach($featuredCities as $i => $city)
            <button class="dp-nav-dot {{ $i === 0 ? 'dp-dot--active' : '' }}"
                    data-goto="{{ $i }}" aria-label="{{ $city->name }}">
                <span class="dp-dot-inner">
                    <span class="dp-dot-name">{{ $city->name }}</span>
                    <span class="dp-dot-bar"><span class="dp-dot-fill"></span></span>
                </span>
            </button>
            @endforeach
        </div>
        <div class="dp-nav-arrows">
            <button class="dp-nav-arrow" id="dp-prev" aria-label="Précédent">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button class="dp-nav-arrow" id="dp-next" aria-label="Suivant">
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    {{-- Vague sombre → crème --}}
    <div class="dp-showcase-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="var(--cream)"/>
        </svg>
    </div>
</section>
@endif


{{-- ══════════════════════════════════════════════
     §3  FILTRES STICKY
══════════════════════════════════════════════ --}}
<div class="dp-filters" id="dp-filters">
    <div class="dt-container dp-filters-inner">
        <div class="dp-filter-tabs" role="tablist">
            <button class="dp-filter-tab dp-filter-tab--active" data-filter="all" role="tab" aria-selected="true">
                Toutes <span class="dp-tab-count">{{ $categoryCounts['all'] ?? 0 }}</span>
            </button>
            @foreach(['historical'=>'Historiques','nature'=>'Nature','coastal'=>'Côtières','urban'=>'Urbain'] as $cat => $lbl)
                @if(($categoryCounts[$cat] ?? 0) > 0)
                <button class="dp-filter-tab" data-filter="{{ $cat }}" role="tab" aria-selected="false">
                    {{ $categoryEmojis[$cat] }} {{ $lbl }}
                    <span class="dp-tab-count">{{ $categoryCounts[$cat] }}</span>
                </button>
                @endif
            @endforeach
        </div>
        <p class="dp-filter-count" id="dp-result-count" aria-live="polite">
            {{ $cities->total() }} destination{{ $cities->total() > 1 ? 's' : '' }}
        </p>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     §4  CATALOGUE — hero cards + grille
══════════════════════════════════════════════ --}}
<section class="dp-catalogue" id="dp-catalogue">
    <div class="dt-container">

        <div class="dp-section-head dt-reveal">
            <div class="dp-section-label">
                <span class="dp-label-bar"></span>
                <span>Toutes les destinations</span>
            </div>
            <h2 class="dp-section-title">Chaque ville, <em>une histoire</em></h2>
        </div>

        <div id="dp-no-results" class="dp-no-results" style="display:none">
            <div class="dp-no-results-icon">🔍</div>
            <p>Aucune destination ne correspond à votre recherche.</p>
            <button id="dp-clear-search" class="dp-clear-btn">Effacer la recherche</button>
        </div>

        {{-- 2 premières cartes : format hero horizontal — utilise $heroCards du controller --}}
        <div class="dp-hero-cards" id="dp-hero-cards">
            @foreach($heroCards as $i => $city)
            @php
                $grad = $destGrads[$city->name] ?? '160deg,#1F6B44,#0D3822';
                $cat  = $city->category ?? 'urban';
            @endphp
            <a href="{{ route('destinations.city', $city->slug) }}"
               class="dp-hero-card dt-reveal"
               data-category="{{ $cat }}"
               data-name="{{ strtolower($city->name) }}"
               data-desc="{{ strtolower($city->description ?? '') }}"
               style="--delay:{{ $i * 0.1 }}s">

                <div class="dp-hcard-media">
                    @if($city->cover_image)
                        <img src="{{ mediaUrl($city->cover_image) }}"
                             alt="{{ $city->name }}, Bénin" class="dp-hcard-img" loading="eager">
                    @else
                        <div class="dp-hcard-img dp-hcard-ph" style="background:linear-gradient({{ $grad }})">
                            <span>{{ $categoryEmojis[$cat] ?? '📍' }}</span>
                        </div>
                    @endif
                    <div class="dp-hcard-overlay"></div>
                </div>

                <div class="dp-hcard-content">
                    <div class="dp-hcard-eyebrow">
                        @if($city->is_featured)<span class="dp-hcard-feat"><i class="fas fa-star"></i> À la une</span>@endif
                        <span class="dp-hcard-cat">{{ $categoryLabels[$cat] ?? '' }}</span>
                    </div>
                    <h3 class="dp-hcard-title">{{ $city->name }}</h3>
                    @if($city->description)
                        <p class="dp-hcard-desc">{{ Str::limit($city->description, 110) }}</p>
                    @endif
                    <div class="dp-hcard-meta">
                        @if($city->offers_count > 0)
                        <span><i class="fas fa-ticket-alt"></i> {{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }}</span>
                        @endif
                        @if($city->offers_min_base_price)
                        <span><i class="fas fa-tag"></i> À partir de {{ number_format($city->offers_min_base_price, 0, ',', ' ') }} FCFA</span>
                        @endif
                    </div>
                    <span class="dp-hcard-cta">
                        Découvrir <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Reste des cartes : grille standard — utilise $gridCards du controller --}}
        <div class="dp-city-grid" id="dp-city-grid">
            @forelse($gridCards as $i => $city)
            @php
                $grad   = $destGrads[$city->name] ?? '160deg,#1F6B44,#0D3822';
                $rating = $city->average_rating > 0 ? number_format($city->average_rating, 1) : null;
                $cat    = $city->category ?? 'urban';
            @endphp

            <a href="{{ route('destinations.city', $city->slug) }}"
               class="dp-city-card dt-reveal"
               data-category="{{ $cat }}"
               data-name="{{ strtolower($city->name) }}"
               data-desc="{{ strtolower($city->description ?? '') }}"
               style="--delay:{{ ($i % 6) * 0.07 }}s">

                <div class="dp-city-img-wrap">
                    <div class="dp-skeleton"></div>
                    @if($city->cover_image)
                        <img src="{{ mediaUrl($city->cover_image) }}"
                             alt="{{ $city->name }}, Bénin" class="dp-city-img" loading="lazy"
                             onload="this.previousElementSibling.style.display='none';this.classList.add('dp-img-loaded')">
                    @else
                        <div class="dp-city-img dp-city-img--ph dp-img-loaded"
                             style="background:linear-gradient({{ $grad }})">
                            <span>{{ $categoryEmojis[$cat] ?? '📍' }}</span>
                        </div>
                    @endif
                    <div class="dp-city-reveal-ov"></div>
                    <div class="dp-city-overlay"></div>

                    <div class="dp-city-img-top">
                        @if($city->is_featured)
                            <span class="dp-city-badge dp-city-badge--feat"><i class="fas fa-star"></i> Featured</span>
                        @else <span></span>
                        @endif
                        @if($city->best_season ?? null)
                            <span class="dp-city-badge dp-city-badge--season"><i class="fas fa-sun"></i> {{ $city->best_season }}</span>
                        @endif
                    </div>
                    @if($city->offers_min_base_price)
                    <div class="dp-city-price-tag">
                        <span class="dp-city-from">À partir de</span>
                        <span class="dp-city-price-val">{{ number_format($city->offers_min_base_price,0,',',' ') }}</span>
                        <span class="dp-city-price-cur">FCFA</span>
                    </div>
                    @endif
                    <div class="dp-city-hover-reveal">
                        <span class="dp-city-hover-name">{{ $city->name }}</span>
                        <span class="dp-city-hover-cta">Découvrir <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>

                <div class="dp-city-body">
                    <div class="dp-city-header">
                        <div>
                            <h3 class="dp-city-name">{{ $city->name }}</h3>
                            <p class="dp-city-loc">
                                <i class="fas fa-map-marker-alt"></i> Bénin
                                @if($city->distance_from_cotonou ?? null) · {{ $city->distance_from_cotonou }} de Cotonou @endif
                            </p>
                        </div>
                        @if($rating)
                        <div class="dp-city-rating"><i class="fas fa-star"></i> {{ $rating }}</div>
                        @endif
                    </div>
                    @if($city->description)
                        <p class="dp-city-desc">{{ Str::limit($city->description, 90) }}</p>
                    @endif
                    <div class="dp-city-meta">
                        @if($city->category)
                            <span class="dp-city-pill"><i class="fas fa-compass"></i> {{ $categoryLabels[$city->category] ?? '' }}</span>
                        @endif
                    </div>
                    <div class="dp-city-foot">
                        <span class="dp-city-count"><i class="fas fa-ticket-alt"></i> {{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }}</span>
                        <span class="dp-city-btn">Voir <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>
            </a>

            @empty
            <div class="dp-empty" style="grid-column:1/-1; text-align:center; padding:60px 24px;">
                <div style="font-size:48px; margin-bottom:12px;">🗺️</div>
                <p style="color:#7a6a58; font-size:15px;">Aucune autre destination disponible pour le moment.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($cities instanceof \Illuminate\Pagination\LengthAwarePaginator && $cities->hasPages())
        <div class="dp-pagination">{{ $cities->links() }}</div>
        @endif

    </div>
</section>


{{-- ══════════════════════════════════════════════
     §5  CTA FINAL
══════════════════════════════════════════════ --}}
<section class="dp-cta">
    <div class="dp-cta-bg" aria-hidden="true"></div>
    <div class="dt-container">
        <div class="dp-cta-inner dt-reveal">
            <div class="dp-cta-badge"><i class="fas fa-compass"></i> Votre aventure commence ici</div>
            <h2 class="dp-cta-title">Vous ne savez pas<br><em>par où commencer ?</em></h2>
            <p class="dp-cta-sub">
                Nos experts locaux construisent votre itinéraire sur mesure en 24h.
                Gratuit, sans engagement.
            </p>
            <div class="dp-cta-btns">
                <a href="{{ route('offers.index') }}" class="dt-btn dt-btn--ambre">
                    <i class="fas fa-search"></i> Voir toutes les expériences
                </a>
                <a href="{{ route('contact') }}" class="dt-btn dt-btn--ghost-dark">
                    <i class="fas fa-headset"></i> Parler à un expert
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {

    /* ── Compteurs animés ── */
    let counted = false;
    const runCounters = () => {
        if (counted) return;
        counted = true;
        document.querySelectorAll('.dp-hstat-num[data-target]').forEach(el => {
            const target = parseInt(el.dataset.target, 10);
            const start  = performance.now();
            const tick   = (now) => {
                const p = Math.min((now - start) / 1600, 1);
                el.textContent = Math.round((1 - Math.pow(1 - p, 3)) * target);
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        });
    };
    setTimeout(runCounters, 400);

    /* ── Showcase magazine ── */
    const slides  = document.querySelectorAll('.dp-slide');
    const dots    = document.querySelectorAll('.dp-nav-dot');
    const fills   = document.querySelectorAll('.dp-dot-fill');
    let current   = 0;
    let autoTimer = null;

    const goTo = (idx) => {
        slides[current]?.classList.remove('dp-slide--active');
        dots[current]?.classList.remove('dp-dot--active');
        if (fills[current]) fills[current].style.animation = 'none';
        current = ((idx % slides.length) + slides.length) % slides.length;
        slides[current]?.classList.add('dp-slide--active');
        dots[current]?.classList.add('dp-dot--active');
        if (fills[current]) {
            fills[current].style.animation = 'none';
            void fills[current].offsetWidth; // reflow
            fills[current].style.animation = 'dp-dot-progress 6s linear forwards';
        }
    };

    const startAuto = () => {
        clearInterval(autoTimer);
        autoTimer = setInterval(() => goTo(current + 1), 6000);
    };

    document.getElementById('dp-prev')?.addEventListener('click', () => { goTo(current - 1); startAuto(); });
    document.getElementById('dp-next')?.addEventListener('click', () => { goTo(current + 1); startAuto(); });
    dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.goto)); startAuto(); }));

    let tx = 0;
    const track = document.getElementById('dp-showcase-track');
    track?.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
    track?.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - tx;
        if (Math.abs(dx) > 50) { goTo(dx < 0 ? current + 1 : current - 1); startAuto(); }
    });

    if (slides.length > 1) { goTo(0); startAuto(); }
    else if (fills[0]) fills[0].style.animation = 'none';

    /* ── Recherche en temps réel ── */
    const searchInput   = document.getElementById('dp-search-input');
    const searchClear   = document.getElementById('dp-search-clear');
    const clearBtn      = document.getElementById('dp-clear-search');
    const noResults     = document.getElementById('dp-no-results');
    const heroCards     = document.querySelectorAll('.dp-hero-card');
    const cityCards     = document.querySelectorAll('.dp-city-card');
    const allCards      = [...heroCards, ...cityCards];
    const heroCardsWrap = document.getElementById('dp-hero-cards');

    const applySearch = (query) => {
        const q = query.trim().toLowerCase();
        searchClear.style.opacity      = q ? '1' : '0';
        searchClear.style.pointerEvents = q ? 'auto' : 'none';

        if (!q) {
            allCards.forEach(c => { c.style.display = ''; c.classList.remove('dp-card-hidden','dp-card-show'); });
            if (heroCardsWrap) heroCardsWrap.style.display = '';
            noResults.style.display = 'none';
            return;
        }

        // Masquer le bloc hero cards, chercher dans toutes les cartes uniformément
        if (heroCardsWrap) heroCardsWrap.style.display = 'none';

        let visible = 0;
        allCards.forEach(card => {
            const name  = card.dataset.name || '';
            const desc  = card.dataset.desc || '';
            const match = name.includes(q) || desc.includes(q);
            if (match) {
                card.style.display = '';
                card.classList.remove('dp-card-hidden');
                card.classList.add('dp-card-show');
                visible++;
            } else {
                card.classList.add('dp-card-hidden');
                card.classList.remove('dp-card-show');
                setTimeout(() => {
                    if (card.classList.contains('dp-card-hidden')) card.style.display = 'none';
                }, 250);
            }
        });

        noResults.style.display = visible === 0 ? 'flex' : 'none';
        document.getElementById('dp-catalogue')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    let debounce;
    searchInput?.addEventListener('input', e => {
        clearTimeout(debounce);
        debounce = setTimeout(() => applySearch(e.target.value), 200);
    });

    const clearSearch = () => {
        if (searchInput) searchInput.value = '';
        applySearch('');
    };
    searchClear?.addEventListener('click', clearSearch);
    clearBtn?.addEventListener('click', clearSearch);

    /* ── Filtres catégorie ── */
    const tabs    = document.querySelectorAll('.dp-filter-tab');
    const countEl = document.getElementById('dp-result-count');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => {
                t.classList.remove('dp-filter-tab--active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('dp-filter-tab--active');
            tab.setAttribute('aria-selected', 'true');

            const filter = tab.dataset.filter;
            let visible  = 0;

            // Reset recherche
            if (searchInput) searchInput.value = '';
            if (searchClear) { searchClear.style.opacity = '0'; searchClear.style.pointerEvents = 'none'; }

            // Hero cards : afficher uniquement si filtre = all
            if (heroCardsWrap) heroCardsWrap.style.display = filter === 'all' ? '' : 'none';

            // Compter les hero cards si filtre actif
            if (filter !== 'all') {
                heroCards.forEach(c => {
                    if (c.dataset.category === filter) visible++;
                });
            }

            // Grille
            cityCards.forEach((card, i) => {
                const match = filter === 'all' || card.dataset.category === filter;
                card.style.display = '';
                card.style.animationDelay = (i % 9 * 0.05) + 's';
                card.classList.toggle('dp-card-hidden', !match);
                card.classList.toggle('dp-card-show', match);
                if (match) visible++;
            });

            // Pour filtre 'all', compter toutes les cartes
            if (filter === 'all') {
                visible = allCards.length;
            }

            noResults.style.display = 'none';
            if (countEl) countEl.textContent = visible + ' destination' + (visible > 1 ? 's' : '');
        });
    });

    /* ── Ombre sticky ── */
    const filtersEl = document.getElementById('dp-filters');
    window.addEventListener('scroll', () => {
        filtersEl?.classList.toggle('dp-filters--scrolled', window.scrollY > 300);
    }, { passive: true });

})();
</script>
@endpush