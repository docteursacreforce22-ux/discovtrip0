@extends('layouts.app')

@section('title', ($country->meta_title ?? 'Découvrir ' . $country->name) . ' — DiscovTrip')

@push('meta')
<meta name="description" content="{{ $country->meta_description ?? 'Découvrez ' . $country->name . ' : ' . $totalOffers . ' expériences authentiques avec des guides locaux certifiés.' }}">
<meta property="og:title" content="{{ $country->name }} — DiscovTrip">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@if($country->cover_image)
<meta property="og:image" content="{{ mediaUrl($country->cover_image) }}">
@endif
@endpush

@push('styles')
    @vite('resources/css/pages/destinations.css')
@endpush

@php
$categoryEmojis = ['urban'=>'🏙️','historical'=>'🏛️','nature'=>'🌿','coastal'=>'🏖️'];
$categoryLabels = ['urban'=>'Urbain','historical'=>'Historique','nature'=>'Nature','coastal'=>'Côtière'];
@endphp

@section('content')

{{-- ══ HERO PAYS ══════════════════════════════════════════ --}}
<section class="dp-hero">

    {{-- Fond : cover_image du pays ou motif wax --}}
    @if($country->cover_image)
        <div style="position:absolute;inset:0;z-index:0">
            <img src="{{ mediaUrl($country->cover_image) }}"
                 alt="{{ $country->name }}"
                 style="width:100%;height:100%;object-fit:cover;opacity:.35">
        </div>
    @else
        <x-hero-bg setting-key="hero_destinations" pattern-id="wp-country" />
    @endif

    <div class="dt-container dp-hero-inner">
        <div class="dp-hero-text">

            {{-- Breadcrumb --}}
            <nav style="margin-bottom:1rem;font-size:13px;opacity:.7" aria-label="Fil d'Ariane">
                <a href="{{ route('destinations') }}" style="color:inherit">Destinations</a>
                <span style="margin:0 6px">›</span>
                <span>{{ $country->name }}</span>
            </nav>

            <div class="dp-hero-eyebrow dp-anim dp-anim-1">
                <span class="dp-eyebrow-dot"></span>
                {{ $country->flag_emoji ?? '' }} {{ $country->continent ?? 'Afrique' }}
                <span class="dp-eyebrow-dot"></span>
            </div>

            <h1 class="dp-hero-title dp-anim dp-anim-2">
                {{ $country->name }}<br><em>à découvrir.</em>
            </h1>

            @if($country->description)
            <p class="dp-hero-sub dp-anim dp-anim-3">
                {{ Str::limit($country->description, 200) }}
            </p>
            @endif
        </div>

        <div class="dp-hero-stats dp-anim dp-anim-3">
            @if($country->capital)
            <div class="dp-hstat">
                <span class="dp-hstat-num" style="font-size:18px">{{ $country->capital }}</span>
                <span class="dp-hstat-lbl">Capitale</span>
            </div>
            <div class="dp-hstat-div"></div>
            @endif
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $cities->total() }}">0</span>
                <span class="dp-hstat-lbl">Destinations</span>
            </div>
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $totalOffers }}">0</span>
                <span class="dp-hstat-lbl">Expériences</span>
            </div>
            @if($country->currency_code)
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <span class="dp-hstat-num" style="font-size:18px">{{ $country->currency_code }}</span>
                <span class="dp-hstat-lbl">{{ $country->currency_name ?? 'Devise' }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="dp-hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none">
            <path d="M0,36 C360,72 1080,0 1440,36 L1440,72 L0,72 Z" fill="var(--f-900)"/>
        </svg>
    </div>
</section>

{{-- ══ VILLES FEATURED ════════════════════════════════════ --}}
@if($featuredCities->count() > 0)
<section class="dp-showcase" id="dp-showcase">
    <div class="dp-showcase-label dt-container">
        <span class="dp-label-bar-light"></span>
        <span>À la une — {{ $country->name }}</span>
    </div>

    <div class="dp-showcase-track" id="dp-showcase-track">
        @foreach($featuredCities as $i => $city)
        @php
            $rating = $city->average_rating > 0 ? number_format($city->average_rating, 1) : '4.8';
        @endphp
        <div class="dp-slide {{ $i === 0 ? 'dp-slide--active' : '' }}" data-index="{{ $i }}">
            <div class="dp-slide-bg">
                @if($city->cover_image)
                    <img src="{{ mediaUrl($city->cover_image) }}" alt="{{ $city->name }}"
                         class="dp-slide-bg-img" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                @else
                    <div class="dp-slide-bg-ph" style="background:linear-gradient(160deg,#1F6B44,#0D3822)"></div>
                @endif
                <div class="dp-slide-bg-overlay"></div>
            </div>
            <div class="dt-container dp-slide-inner">
                <div class="dp-slide-left">
                    <div class="dp-slide-eyebrow">
                        <span>{{ $country->flag_emoji ?? '' }} {{ $country->name }}</span>
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
                        <span class="dp-slide-chip dp-slide-chip--rating">
                            <i class="fas fa-star"></i> {{ $rating }} / 5
                        </span>
                    </div>
                    <a href="{{ route('destinations.city', $city->slug) }}" class="dp-slide-cta">
                        Découvrir {{ $city->name }}
                        <span class="dp-cta-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
                <div class="dp-slide-num" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
        @endforeach
    </div>

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
            <button class="dp-nav-arrow" id="dp-prev" aria-label="Précédent"><i class="fas fa-arrow-left"></i></button>
            <button class="dp-nav-arrow" id="dp-next" aria-label="Suivant"><i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <div class="dp-showcase-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="var(--cream)"/>
        </svg>
    </div>
</section>
@endif

{{-- ══ INFOS PAYS (optionnel — visible si rempli en admin) ══ --}}
@if($country->history || $country->culture || $country->practical_info)
<section style="background:var(--f-900);padding:4rem 0">
    <div class="dt-container">
        <div class="dp-section-head dt-reveal" style="color:var(--cream)">
            <div class="dp-section-label">
                <span class="dp-label-bar-light"></span>
                <span>Tout savoir sur {{ $country->name }}</span>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem;margin-top:1.5rem">
            @if($country->history)
            <div style="background:rgba(255,255,255,.05);border-radius:12px;padding:1.5rem">
                <div style="font-size:1.5rem;margin-bottom:8px">🏛️</div>
                <h3 style="color:var(--cream);font-size:16px;margin-bottom:8px">Histoire</h3>
                <p style="color:rgba(255,255,255,.65);font-size:14px;line-height:1.7">{{ $country->history }}</p>
            </div>
            @endif
            @if($country->culture)
            <div style="background:rgba(255,255,255,.05);border-radius:12px;padding:1.5rem">
                <div style="font-size:1.5rem;margin-bottom:8px">🎭</div>
                <h3 style="color:var(--cream);font-size:16px;margin-bottom:8px">Culture</h3>
                <p style="color:rgba(255,255,255,.65);font-size:14px;line-height:1.7">{{ $country->culture }}</p>
            </div>
            @endif
            @if($country->practical_info)
            <div style="background:rgba(255,255,255,.05);border-radius:12px;padding:1.5rem">
                <div style="font-size:1.5rem;margin-bottom:8px">🧳</div>
                <h3 style="color:var(--cream);font-size:16px;margin-bottom:8px">Infos pratiques</h3>
                <p style="color:rgba(255,255,255,.65);font-size:14px;line-height:1.7">{{ $country->practical_info }}</p>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ══ GRILLE VILLES ══════════════════════════════════════ --}}
<section class="dp-catalogue" id="dp-catalogue">
    <div class="dt-container">

        <div class="dp-section-head dt-reveal">
            <div class="dp-section-label">
                <span class="dp-label-bar"></span>
                <span>Villes à visiter</span>
            </div>
            <h2 class="dp-section-title">{{ $country->name }} — <em>chaque ville, une âme</em></h2>
        </div>

        {{-- Hero cards (2 premières) --}}
        @if($heroCards->count() > 0)
        <div class="dp-hero-cards">
            @foreach($heroCards as $i => $city)
            @php $cat = $city->category ?? 'urban'; @endphp
            <a href="{{ route('destinations.city', $city->slug) }}"
               class="dp-hero-card dt-reveal" style="--delay:{{ $i * 0.1 }}s">
                <div class="dp-hcard-media">
                    @if($city->cover_image)
                        <img src="{{ mediaUrl($city->cover_image) }}" alt="{{ $city->name }}"
                             class="dp-hcard-img" loading="eager">
                    @else
                        <div class="dp-hcard-img dp-hcard-ph" style="background:linear-gradient(160deg,#1F6B44,#0D3822)">
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
                    <span class="dp-hcard-cta">Découvrir <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Grille standard --}}
        <div class="dp-city-grid">
            @forelse($gridCards as $i => $city)
            @php
                $cat    = $city->category ?? 'urban';
                $rating = $city->average_rating > 0 ? number_format($city->average_rating, 1) : null;
            @endphp
            <a href="{{ route('destinations.city', $city->slug) }}"
               class="dp-city-card dt-reveal" style="--delay:{{ ($i % 6) * 0.07 }}s">
                <div class="dp-city-img-wrap">
                    <div class="dp-skeleton"></div>
                    @if($city->cover_image)
                        <img src="{{ mediaUrl($city->cover_image) }}" alt="{{ $city->name }}"
                             class="dp-city-img" loading="lazy"
                             onload="this.previousElementSibling.style.display='none';this.classList.add('dp-img-loaded')">
                    @else
                        <div class="dp-city-img dp-city-img--ph dp-img-loaded"
                             style="background:linear-gradient(160deg,#1F6B44,#0D3822)">
                            <span>{{ $categoryEmojis[$cat] ?? '📍' }}</span>
                        </div>
                    @endif
                    <div class="dp-city-overlay"></div>
                    @if($city->offers_min_base_price)
                    <div class="dp-city-price-tag">
                        <span class="dp-city-from">À partir de</span>
                        <span class="dp-city-price-val">{{ number_format($city->offers_min_base_price,0,',',' ') }}</span>
                        <span class="dp-city-price-cur">FCFA</span>
                    </div>
                    @endif
                </div>
                <div class="dp-city-body">
                    <div class="dp-city-header">
                        <div>
                            <h3 class="dp-city-name">{{ $city->name }}</h3>
                            <p class="dp-city-loc">
                                <i class="fas fa-map-marker-alt"></i> {{ $country->name }}
                                @if($city->region) · {{ $city->region }} @endif
                            </p>
                        </div>
                        @if($rating)
                        <div class="dp-city-rating"><i class="fas fa-star"></i> {{ $rating }}</div>
                        @endif
                    </div>
                    @if($city->description)
                        <p class="dp-city-desc">{{ Str::limit($city->description, 90) }}</p>
                    @endif
                    <div class="dp-city-foot">
                        <span class="dp-city-count">
                            <i class="fas fa-ticket-alt"></i>
                            {{ $city->offers_count }} expérience{{ $city->offers_count > 1 ? 's' : '' }}
                        </span>
                        <span class="dp-city-btn">Voir <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>
            </a>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px 24px">
                    <p style="color:#7a6a58">Aucune ville disponible pour le moment.</p>
                </div>
            @endforelse
        </div>

        @if($cities->hasPages())
        <div class="dp-pagination">{{ $cities->links() }}</div>
        @endif

    </div>
</section>

{{-- ══ CTA ════════════════════════════════════════════════ --}}
<section class="dp-cta">
    <div class="dp-cta-bg" aria-hidden="true"></div>
    <div class="dt-container">
        <div class="dp-cta-inner dt-reveal">
            <div class="dp-cta-badge"><i class="fas fa-compass"></i> {{ $country->name }}</div>
            <h2 class="dp-cta-title">Prêt à explorer<br><em>{{ $country->name }} ?</em></h2>
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
    // Compteurs
    document.querySelectorAll('.dp-hstat-num[data-target]').forEach(el => {
        const target = parseInt(el.dataset.target, 10);
        const start  = performance.now();
        const tick   = (now) => {
            const p = Math.min((now - start) / 1400, 1);
            el.textContent = Math.round((1 - Math.pow(1 - p, 3)) * target);
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    // Showcase (copié de destinations.blade.php)
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
            void fills[current].offsetWidth;
            fills[current].style.animation = 'dp-dot-progress 6s linear forwards';
        }
    };
    const startAuto = () => { clearInterval(autoTimer); autoTimer = setInterval(() => goTo(current + 1), 6000); };
    document.getElementById('dp-prev')?.addEventListener('click', () => { goTo(current - 1); startAuto(); });
    document.getElementById('dp-next')?.addEventListener('click', () => { goTo(current + 1); startAuto(); });
    dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.goto)); startAuto(); }));
    if (slides.length > 1) { goTo(0); startAuto(); }
})();
</script>
@endpush
