@extends('layouts.app')

@section('title', 'Destinations — Explorez le monde avec DiscovTrip')

@push('meta')
<meta name="description" content="Découvrez {{ $totalCountries }} pays et {{ $totalCities }} destinations authentiques. {{ $totalOffers }} expériences uniques avec des guides locaux certifiés.">
<meta property="og:title" content="Destinations — DiscovTrip">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@endpush

@push('styles')
    @vite('resources/css/pages/destinations.css')
@endpush

@section('content')

{{-- ══ HERO ════════════════════════════════════════════════ --}}
<section class="dp-hero">
    <x-hero-bg setting-key="hero_destinations" pattern-id="wp-dp" />
    <div class="dt-container dp-hero-inner">
        <div class="dp-hero-text">
            <div class="dp-hero-eyebrow dp-anim dp-anim-1">
                <span class="dp-eyebrow-dot"></span>
                Afrique · Monde
                <span class="dp-eyebrow-dot"></span>
            </div>
            <h1 class="dp-hero-title dp-anim dp-anim-2">
                Explorez<br>le monde,<br><em>autrement.</em>
            </h1>
            <p class="dp-hero-sub dp-anim dp-anim-3">
                {{ $totalCountries }} pays, {{ $totalCities }} destinations,
                {{ $totalOffers }} expériences authentiques avec des guides locaux certifiés.
            </p>
        </div>
        <div class="dp-hero-stats dp-anim dp-anim-3">
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $totalCountries }}">0</span>
                <span class="dp-hstat-lbl">Pays</span>
            </div>
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $totalCities }}">0</span>
                <span class="dp-hstat-lbl">Destinations</span>
            </div>
            <div class="dp-hstat-div"></div>
            <div class="dp-hstat">
                <span class="dp-hstat-num" data-target="{{ $totalOffers }}">0</span>
                <span class="dp-hstat-lbl">Expériences</span>
            </div>
        </div>
    </div>
    <div class="dp-hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none">
            <path d="M0,36 C360,72 1080,0 1440,36 L1440,72 L0,72 Z" fill="var(--cream)"/>
        </svg>
    </div>
</section>

{{-- ══ GRILLE PAYS ════════════════════════════════════════ --}}
<section class="dp-catalogue" style="background:var(--cream)">
    <div class="dt-container">

        <div class="dp-section-head dt-reveal">
            <div class="dp-section-label">
                <span class="dp-label-bar"></span>
                <span>Nos destinations</span>
            </div>
            <h2 class="dp-section-title">Choisissez votre <em>pays</em></h2>
        </div>

        <div class="dp-city-grid">
            @forelse($countries as $i => $country)
            <a href="{{ route('destinations.country', $country->slug) }}"
               class="dp-city-card dt-reveal"
               style="--delay:{{ ($i % 6) * 0.07 }}s">

                <div class="dp-city-img-wrap">
                    @if($country->cover_image)
                        <img src="{{ mediaUrl($country->cover_image) }}"
                             alt="{{ $country->name }}"
                             class="dp-city-img dp-img-loaded" loading="lazy">
                    @else
                        <div class="dp-city-img dp-city-img--ph dp-img-loaded"
                             style="background:linear-gradient(160deg,#1F6B44,#0D3822)">
                            <span style="font-size:48px">{{ $country->flag_emoji ?? '🌍' }}</span>
                        </div>
                    @endif
                    <div class="dp-city-overlay"></div>

                    <div class="dp-city-img-top">
                        @if($country->is_featured)
                            <span class="dp-city-badge dp-city-badge--feat"><i class="fas fa-star"></i> Featured</span>
                        @else
                            <span></span>
                        @endif
                        @if($country->continent)
                            <span class="dp-city-badge dp-city-badge--season">{{ $country->continent }}</span>
                        @endif
                    </div>

                    <div class="dp-city-hover-reveal">
                        <span class="dp-city-hover-name">{{ $country->flag_emoji ?? '' }} {{ $country->name }}</span>
                        <span class="dp-city-hover-cta">Découvrir <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>

                <div class="dp-city-body">
                    <div class="dp-city-header">
                        <div>
                            <h3 class="dp-city-name">
                                {{ $country->flag_emoji ?? '' }} {{ $country->name }}
                            </h3>
                            <p class="dp-city-loc">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $country->continent ?? 'Afrique' }}
                                @if($country->capital) · {{ $country->capital }} @endif
                            </p>
                        </div>
                    </div>
                    @if($country->description)
                        <p class="dp-city-desc">{{ Str::limit($country->description, 90) }}</p>
                    @endif
                    <div class="dp-city-foot">
                        <span class="dp-city-count">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $country->cities_count ?? 0 }} ville{{ ($country->cities_count ?? 0) > 1 ? 's' : '' }}
                        </span>
                        <span class="dp-city-btn">Explorer <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>
            </a>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px 24px">
                    <div style="font-size:48px;margin-bottom:12px">🌍</div>
                    <p style="color:#7a6a58">Destinations bientôt disponibles.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
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
</script>
@endpush
