@extends('layouts.app')

@section('title', 'À propos — DiscovTrip')

@push('meta')
<meta name="description" content="Découvrez DiscovTrip, la plateforme d'expériences authentiques au Bénin. Notre mission, notre équipe, nos valeurs.">
<meta property="og:title" content="À propos — DiscovTrip">
<meta property="og:description" content="Découvrez DiscovTrip, la plateforme d'expériences authentiques au Bénin.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@endpush

@push('styles')
    @vite('resources/css/pages/about.css')
@endpush

@section('content')

{{-- ════════════════════════════════════════════
     §1  HERO — fond sombre, wax, stats animées
════════════════════════════════════════════ --}}
<section class="ab-hero">

    {{-- Fond : image DB ou var(--f-900) + motif wax --}}
    <x-hero-bg setting-key="hero_about" pattern-id="wp-ab" />

    <div class="dt-container ab-hero-inner">

        <div class="ab-hero-eyebrow ab-anim ab-anim-1">
            <span class="ab-eyebrow-dot"></span>
            Notre Histoire
            <span class="ab-eyebrow-dot"></span>
        </div>

        <h1 class="ab-hero-title ab-anim ab-anim-2">
            {!! nl2br(e($hero['title'])) !!}
        </h1>

        <p class="ab-hero-sub ab-anim ab-anim-3">
            {{ $hero['subtitle'] }}
        </p>

        {{-- Stats dynamiques --}}
        <div class="ab-hero-stats ab-anim ab-anim-4">
            <div class="ab-hstat">
                <span class="ab-hstat-num" data-target="{{ $stats['cities'] }}">0</span>
                <span class="ab-hstat-lbl">Destinations</span>
            </div>
            <div class="ab-hstat-div"></div>
            <div class="ab-hstat">
                <span class="ab-hstat-num" data-target="{{ $stats['offers'] }}">0</span>
                <span class="ab-hstat-lbl">Expériences</span>
            </div>
            <div class="ab-hstat-div"></div>
            <div class="ab-hstat">
                <span class="ab-hstat-num ab-hstat-num--text">{{ $stats['travelers'] }}</span>
                <span class="ab-hstat-lbl">Voyageurs</span>
            </div>
            <div class="ab-hstat-div"></div>
            <div class="ab-hstat">
                <div class="ab-hstat-row">
                    <span class="ab-hstat-num ab-hstat-num--text">{{ $stats['rating'] }}</span>
                    <span class="ab-hstat-sup">★</span>
                </div>
                <span class="ab-hstat-lbl">Note moyenne</span>
            </div>
        </div>

    </div>

    {{-- Vague --}}
    <div class="ab-hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none">
            <path d="M0,36 C360,72 1080,0 1440,36 L1440,72 L0,72 Z" fill="var(--cream)"/>
        </svg>
    </div>
</section>


{{-- ════════════════════════════════════════════
     §2  HISTOIRE — frise chronologique
════════════════════════════════════════════ --}}
<section class="ab-timeline">
    <div class="dt-container">

        <div class="ab-section-head dt-reveal">
            <div class="ab-section-label">
                <span class="ab-label-bar"></span>
                <span>Notre parcours</span>
            </div>
            <h2 class="ab-section-title">De l'idée à <em>la réalité</em></h2>
        </div>

        <div class="ab-tl-track">
            <div class="ab-tl-line" aria-hidden="true"></div>
            @foreach($timeline as $i => $step)
            <div class="ab-tl-item dt-reveal {{ $i % 2 === 0 ? 'ab-tl-item--left' : 'ab-tl-item--right' }}"
                 style="--delay:{{ $i * 0.12 }}s">
                <div class="ab-tl-dot" aria-hidden="true">
                    <span>{{ $step['year'] }}</span>
                </div>
                <div class="ab-tl-card">
                    <div class="ab-tl-year">{{ $step['year'] }}</div>
                    <h3 class="ab-tl-title">{{ $step['title'] }}</h3>
                    <p class="ab-tl-text">{{ $step['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════
     §3  MISSION + VALEURS
════════════════════════════════════════════ --}}
<section class="ab-mission">
    <div class="dt-container ab-mission-grid">

        {{-- Visuel flottant --}}
        <div class="ab-mission-visual dt-reveal">
            <div class="ab-mv-orb" aria-hidden="true"></div>
            <div class="ab-mv-card ab-mv-card--1">
                <span class="ab-mv-emoji">🕌</span>
                <span class="ab-mv-label">Patrimoine culturel</span>
            </div>
            <div class="ab-mv-card ab-mv-card--2">
                <span class="ab-mv-emoji">🌊</span>
                <span class="ab-mv-label">Nature préservée</span>
            </div>
            <div class="ab-mv-card ab-mv-card--3">
                <span class="ab-mv-emoji">🍽️</span>
                <span class="ab-mv-label">Gastronomie locale</span>
            </div>
            <div class="ab-mv-card ab-mv-card--4">
                <span class="ab-mv-emoji">🎭</span>
                <span class="ab-mv-label">Traditions vivantes</span>
            </div>
        </div>

        {{-- Texte mission --}}
        <div class="ab-mission-content dt-reveal" style="--delay:.15s">
            <div class="ab-section-label">
                <span class="ab-label-bar"></span>
                <span>Notre Mission</span>
            </div>
            <h2 class="ab-section-title">
                {{ $mission['title'] }}
            </h2>
            <p class="ab-body-text">{{ $mission['text1'] }}</p>
            <p class="ab-body-text">{{ $mission['text2'] }}</p>

            <div class="ab-values">
                @foreach($values as $val)
                <div class="ab-value">
                    <div class="ab-value-icon">
                        <i class="fas fa-{{ $val['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="ab-value-title">{{ $val['title'] }}</div>
                        <div class="ab-value-text">{{ $val['desc'] ?? $val['text'] ?? '' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════
     §4  ÉQUIPE — dynamique depuis TeamMember
════════════════════════════════════════════ --}}
<section class="ab-team">
    <div class="dt-container">

        <div class="ab-section-head dt-reveal">
            <div class="ab-section-label ab-section-label--light">
                <span class="ab-label-bar ab-label-bar--light"></span>
                <span>Notre Équipe</span>
            </div>
            <h2 class="ab-section-title ab-section-title--light">
                Des passionnés du <em>Bénin</em>
            </h2>
        </div>

        @if($team->count() > 0)
        <div class="ab-team-grid">
            @foreach($team as $i => $member)
            <div class="ab-team-card dt-reveal" style="--delay:{{ ($i % 4) * 0.1 }}s">

                {{-- Photo ou initiales --}}
                <div class="ab-team-avatar-wrap">
                    @if($member->photo)
                        <img src="{{ mediaUrl($member->photo) }}"
                             alt="{{ $member->name }}"
                             class="ab-team-photo" loading="lazy">
                    @else
                        <div class="ab-team-initials">
                            {{ $member->initials }}
                        </div>
                    @endif
                </div>

                <div class="ab-team-body">
                    <h3 class="ab-team-name">{{ $member->name }}</h3>
                    <div class="ab-team-role">{{ $member->role }}</div>
                    <p class="ab-team-bio">{{ $member->bio }}</p>

                    @if($member->linkedin_url || $member->email)
                    <div class="ab-team-links">
                        @if($member->linkedin_url)
                        <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener noreferrer"
                           class="ab-team-link" aria-label="LinkedIn de {{ $member->name }}">
                            <i class="fab fa-linkedin" aria-hidden="true"></i>
                        </a>
                        @endif
                        @if($member->email)
                        <a href="mailto:{{ $member->email }}"
                           class="ab-team-link" aria-label="Email de {{ $member->name }}">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Fallback si TeamMember vide --}}
        <div class="ab-team-grid">
            @foreach([
                ['initials'=>'AK','name'=>'Adéola Koffi',    'role'=>'Fondatrice & CEO',            'bio'=>'Passionnée de tourisme culturel, Adéola a décidé de mettre le Bénin sur la carte du tourisme mondial.'],
                ['initials'=>'JD','name'=>'Jean-Marc Dossou','role'=>'Directeur des Expériences',   'bio'=>'Guide certifié avec 12 ans d\'expérience, Jean-Marc sélectionne et forme chaque partenaire DiscovTrip.'],
                ['initials'=>'FB','name'=>'Fatou Bamba',      'role'=>'Responsable Partenariats',   'bio'=>'Ancienne attachée culturelle, Fatou bâtit les ponts entre DiscovTrip et les communautés locales.'],
                ['initials'=>'RS','name'=>'Rodrigue Sèmaho',  'role'=>'Directeur Technologie',      'bio'=>'Développeur full-stack, Rodrigue a construit la plateforme pour offrir la meilleure expérience digitale.'],
            ] as $i => $member)
            <div class="ab-team-card dt-reveal" style="--delay:{{ $i * 0.1 }}s">
                <div class="ab-team-avatar-wrap">
                    <div class="ab-team-initials">{{ $member['initials'] }}</div>
                </div>
                <div class="ab-team-body">
                    <h3 class="ab-team-name">{{ $member['name'] }}</h3>
                    <div class="ab-team-role">{{ $member['role'] }}</div>
                    <p class="ab-team-bio">{{ $member['bio'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>


{{-- ════════════════════════════════════════════
     §5  CONFIANCE
════════════════════════════════════════════ --}}
<section class="ab-trust">
    <div class="dt-container">

        <div class="ab-section-head ab-section-head--center dt-reveal">
            <div class="ab-section-label">
                <span class="ab-label-bar"></span>
                <span>Ils nous font confiance</span>
            </div>
            <h2 class="ab-section-title">Votre sécurité,<br><em>notre priorité</em></h2>
        </div>

        <div class="ab-trust-grid">
            @foreach($trustBadges as $i => $badge)
            <div class="ab-trust-item dt-reveal" style="--delay:{{ $i * 0.08 }}s">
                <div class="ab-trust-icon">
                    <i class="fas fa-{{ $badge['icon'] }}" aria-hidden="true"></i>
                </div>
                <div class="ab-trust-title">{{ $badge['title'] }}</div>
                <div class="ab-trust-sub">{{ $badge['sub'] }}</div>
            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════
     §6  CTA FINAL
════════════════════════════════════════════ --}}
<section class="ab-cta">
    <div class="ab-cta-wax" aria-hidden="true">
        <div class="ab-cta-glow ab-cta-glow-1"></div>
        <div class="ab-cta-glow ab-cta-glow-2"></div>
    </div>
    <div class="dt-container ab-cta-inner dt-reveal">
        <div class="ab-cta-badge">
            <i class="fas fa-compass" aria-hidden="true"></i> Votre aventure commence ici
        </div>
        <h2 class="ab-cta-title">
            Prêt à découvrir le Bénin <em>autrement</em> ?
        </h2>
        <p class="ab-cta-sub">
            Rejoignez plus de 500 voyageurs qui ont déjà vécu l'expérience DiscovTrip.
        </p>
        <div class="ab-cta-actions">
            <a href="{{ route('offers.index') }}" class="dt-btn dt-btn--ambre">
                <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                Voir toutes les expériences
            </a>
            <a href="{{ route('contact') }}" class="dt-btn dt-btn--ghost-dark">
                <i class="fas fa-headset" aria-hidden="true"></i>
                Nous contacter
            </a>
        </div>
    </div>
</section>

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

    /* ── Compteurs animés ── */
    let counted = false;
    const runCounters = () => {
        if (counted) return;
        counted = true;
        document.querySelectorAll('.ab-hstat-num[data-target]').forEach(el => {
            const target = parseInt(el.dataset.target, 10);
            const start  = performance.now();
            const tick   = now => {
                const p = Math.min((now - start) / 1400, 1);
                el.textContent = Math.round((1 - Math.pow(1 - p, 3)) * target);
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        });
    };
    setTimeout(runCounters, 300);

})();
</script>
@endpush