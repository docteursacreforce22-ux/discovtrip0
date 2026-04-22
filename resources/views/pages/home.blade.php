@extends('layouts.app')

@section('title', 'DiscovTrip — Découvrez le Bénin Authentique & Premium')

{{-- CORRECTION : @section('description') remplacé par @push('meta') --}}
@push('meta')
<meta name="description" content="Réservez des expériences uniques au Bénin. Culture, nature, gastronomie — guidé par des experts locaux qui connaissent chaque secret du pays.">
<meta property="og:title" content="DiscovTrip — L'Afrique Authentique">
<meta property="og:description" content="Expériences premium au Bénin conçues par des experts locaux.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ asset('images/og-default.jpg') }}">
@endpush

@push('styles')
    @vite('resources/css/pages/home.css')
@endpush

@section('content')

{{-- §1 HERO avec slideshow 3 images --}}
<section class="hp-hero"
         aria-label="Bannière principale"
         x-data="{
             slide: 0,
             slides: [
                 { src: '{{ \App\Providers\AppServiceProvider::heroImage("hero_home_1", asset("images/hero.jpg")) }}',   caption: 'Cotonou · Bénin' },
                 { src: '{{ \App\Providers\AppServiceProvider::heroImage("hero_home_2", asset("images/hero-2.jpg")) }}', caption: 'Ouidah · Histoire & Culture' },
                 { src: '{{ \App\Providers\AppServiceProvider::heroImage("hero_home_3", asset("images/hero-3.jpg")) }}', caption: 'Ganvié · Venise de l\'Afrique' },
             ],
             init() {
                 setInterval(() => { this.slide = (this.slide + 1) % this.slides.length; }, 6000);
             }
         }">

    {{-- Fond slideshow --}}
    <div class="hp-hero-bg" aria-hidden="true">
        <template x-for="(s, i) in slides" :key="i">
            <img :src="s.src"
                 :alt="s.caption"
                 class="hp-hero-img hp-hero-slide"
                 :class="{ 'hp-hero-slide--active': slide === i }"
                 loading="eager"
                 fetchpriority="high"
                 decoding="async"
                 width="1920" height="1080">
        </template>
        <div class="hp-hero-overlay"></div>
        <div class="hp-hero-grain"></div>
    </div>

    <div class="hp-hero-content dt-container">

        <p class="hp-hero-eyebrow">
            <span class="hp-hero-eyebrow-dot" aria-hidden="true"></span>
            <span x-text="slides[slide].caption">Bénin · Afrique de l'Ouest</span>
        </p>

        <h1 class="hp-hero-title">
            L'Afrique<br>
            <em class="hp-hero-title-accent">authentique</em><br>
            vous attend
        </h1>

        <p class="hp-hero-sub">
            Expériences premium au cœur du Bénin.
            Guidé par des experts locaux, chaque voyage devient une mémoire inoubliable.
        </p>

        <div class="hp-search" role="search" aria-label="Rechercher une expérience">
            <form action="{{ route('offers.index') }}" method="GET" class="hp-search-form">
                <div class="hp-search-field">
                    <label for="hs-city" class="hp-search-label">Destination</label>
                    <select id="hs-city" name="city" class="hp-search-select">
                        <option value="">Toutes les villes</option>
                        @foreach($featuredCities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hp-search-sep" aria-hidden="true"></div>
                <div class="hp-search-field">
                    <label for="hs-category" class="hp-search-label">Type d'expérience</label>
                    <select id="hs-category" name="category" class="hp-search-select">
                        <option value="">Toutes les catégories</option>
                        <option value="cultural">Culture &amp; Histoire</option>
                        <option value="nature">Nature &amp; Aventure</option>
                        <option value="gastronomy">Gastronomie</option>
                        <option value="adventure">Aventure</option>
                        <option value="wellness">Bien-être</option>
                        <option value="urban">Urbain</option>
                    </select>
                </div>
                <button type="submit" class="hp-search-btn" aria-label="Lancer la recherche">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span>Rechercher</span>
                </button>
            </form>
        </div>

        <div class="hp-hero-trust" aria-label="Garanties DiscovTrip">
            <div class="hp-trust-item"><i class="fas fa-star" aria-hidden="true"></i> 4.9 / 5 satisfaction</div>
            <span class="hp-trust-sep" aria-hidden="true"></span>
            <div class="hp-trust-item"><i class="fas fa-shield-alt" aria-hidden="true"></i> Paiement sécurisé</div>
            <span class="hp-trust-sep" aria-hidden="true"></span>
            <div class="hp-trust-item"><i class="fas fa-undo-alt" aria-hidden="true"></i> Annulation gratuite</div>
        </div>

    </div>

    {{-- Indicateurs de slide --}}
    <div class="hp-hero-dots" aria-label="Navigation slideshow" role="tablist">
        <template x-for="(s, i) in slides" :key="i">
            <button class="hp-hero-dot"
                    :class="{ 'hp-hero-dot--active': slide === i }"
                    @click="slide = i"
                    :aria-label="'Slide ' + (i+1)"
                    role="tab"
                    :aria-selected="(slide === i).toString()">
            </button>
        </template>
    </div>

    <div class="hp-scroll-hint" aria-hidden="true">
        <div class="hp-scroll-hint-line"></div>
        <span class="hp-scroll-hint-label">Découvrir</span>
    </div>

</section>

{{-- §2 STATS --}}
<section class="hp-stats" aria-label="Chiffres clés DiscovTrip">
    <div class="hp-stats-inner dt-container">
        <div class="hp-stat-item dt-reveal dt-delay-1">
            <div class="hp-stat-icon" aria-hidden="true"><i class="fas fa-users"></i></div>
            <div class="hp-stat-num-wrap">
                <span class="hp-stat-num" data-target="500">0</span>
                <span class="hp-stat-suffix" aria-hidden="true">+</span>
            </div>
            <p class="hp-stat-label">Voyageurs satisfaits</p>
        </div>
        <div class="hp-stat-sep" aria-hidden="true"></div>
        <div class="hp-stat-item dt-reveal dt-delay-2">
            <div class="hp-stat-icon" aria-hidden="true"><i class="fas fa-compass"></i></div>
            <div class="hp-stat-num-wrap">
                @if($totalOffers > 0)
                    <span class="hp-stat-num" data-target="{{ $totalOffers }}">0</span>
                @else
                    <span class="hp-stat-num">—</span>
                @endif
            </div>
            <p class="hp-stat-label">Expériences uniques</p>
        </div>
        <div class="hp-stat-sep" aria-hidden="true"></div>
        <div class="hp-stat-item dt-reveal dt-delay-3">
            <div class="hp-stat-icon" aria-hidden="true"><i class="fas fa-map-marked-alt"></i></div>
            <div class="hp-stat-num-wrap">
                <span class="hp-stat-num" data-target="{{ $totalCities }}">0</span>
            </div>
            <p class="hp-stat-label">Destinations explorées</p>
        </div>
        <div class="hp-stat-sep" aria-hidden="true"></div>
        <div class="hp-stat-item dt-reveal dt-delay-4">
            <div class="hp-stat-icon" aria-hidden="true"><i class="fas fa-star"></i></div>
            <div class="hp-stat-num-wrap">
                <span class="hp-stat-num" data-target="100">0</span>
                <span class="hp-stat-suffix" aria-hidden="true">%</span>
            </div>
            <p class="hp-stat-label">Guides locaux certifiés</p>
        </div>
    </div>
</section>

{{-- §3 MARQUEE --}}
<div class="hp-marquee" aria-hidden="true" role="presentation">
    <div class="hp-marquee-track">
        @php $marqueeItems = ['Cotonou','Ouidah','Abomey','Ganvié','Grand-Popo','Natitingou','Porto-Novo','Parakou','Pendjari','Cové']; @endphp
        @foreach(array_merge($marqueeItems, $marqueeItems) as $item)
            <span class="hp-marquee-item">{{ $item }}</span>
            <span class="hp-marquee-dot">✦</span>
        @endforeach
    </div>
</div>

{{-- §4 SPOTLIGHT --}}
@if($spotlight)
<section class="hp-spotlight" aria-labelledby="hp-spotlight-title">
    <div class="hp-spotlight-inner dt-container">

        <div class="hp-spotlight-media dt-reveal">
            <div class="hp-spotlight-img-wrap">
                <img src="{{ mediaUrl($spotlight->image) }}"
                     alt="{{ $spotlight->title }}"
                     class="hp-spotlight-img"
                     loading="lazy" decoding="async" width="600" height="750">
            </div>
            @if($spotlight->badge_text)
            <div class="hp-spotlight-badge">
                @if($spotlight->badge_icon)
                <div class="hp-spotlight-badge-icon" aria-hidden="true"><i class="fas {{ $spotlight->badge_icon }}"></i></div>
                @endif
                <p class="hp-spotlight-badge-text">{{ $spotlight->badge_text }}</p>
            </div>
            @endif
        </div>

        <div class="hp-spotlight-content dt-reveal dt-delay-2">
            <p class="hp-spotlight-eyebrow">
                <i class="fas fa-landmark" aria-hidden="true"></i>
                {{ $spotlight->badge_text ?? 'Bénin · Culture & Histoire' }}
            </p>
            <h2 class="hp-spotlight-title" id="hp-spotlight-title">
                @if($spotlight->highlight_word && Str::contains($spotlight->title, $spotlight->highlight_word))
                    {!! str_replace(e($spotlight->highlight_word), '<em class="hp-spotlight-title-accent">'.e($spotlight->highlight_word).'</em>', e($spotlight->title)) !!}
                @else
                    {{ $spotlight->title }}
                @endif
            </h2>
            @if($spotlight->subtitle)
                <p class="hp-spotlight-subtitle">{{ $spotlight->subtitle }}</p>
            @endif
            @if($spotlight->description)
                <p class="hp-spotlight-desc">{{ $spotlight->description }}</p>
            @endif
            @if($spotlight->stat1_value || $spotlight->stat2_value || $spotlight->stat3_value)
            <div class="hp-spotlight-stats">
                @if($spotlight->stat1_value)
                    <div class="hp-spot-stat">
                        <span class="hp-spot-stat-num">{{ $spotlight->stat1_value }}</span>
                        <span class="hp-spot-stat-lbl">{{ $spotlight->stat1_label }}</span>
                    </div>
                @endif
                @if($spotlight->stat2_value)
                    <div class="hp-spot-stat">
                        <span class="hp-spot-stat-num">{{ $spotlight->stat2_value }}</span>
                        <span class="hp-spot-stat-lbl">{{ $spotlight->stat2_label }}</span>
                    </div>
                @endif
                @if($spotlight->stat3_value)
                    <div class="hp-spot-stat">
                        <span class="hp-spot-stat-num">{{ $spotlight->stat3_value }}</span>
                        <span class="hp-spot-stat-lbl">{{ $spotlight->stat3_label }}</span>
                    </div>
                @endif
            </div>
            @endif
        </div>{{-- fin hp-spotlight-content --}}

    </div>{{-- fin hp-spotlight-inner --}}
    {{-- CORRECTION : </div> orpheline supprimée ici --}}
</section>
@endif

{{-- §5 OFFRES --}}
<section class="hp-offers" aria-labelledby="hp-offers-title">
    <div class="dt-container">
        <header class="hp-section-header">
            <div>
                <div class="hp-chip hp-chip--ambre"><i class="fas fa-compass" aria-hidden="true"></i> À la une</div>
                <h2 class="hp-section-title" id="hp-offers-title">
                    Nos expériences<br><span class="hp-gradient-ambre">incontournables</span>
                </h2>
            </div>
            <a href="{{ route('offers.index') }}" class="hp-view-all" aria-label="Voir toutes les expériences">
                Voir toutes les expériences <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </header>

        @if($featuredOffers->isNotEmpty())
        <div class="hp-offers-grid">
            @foreach($featuredOffers as $index => $offer)
            <article class="hp-ocard {{ $index === 0 ? 'hp-ocard--featured' : '' }} dt-reveal dt-delay-{{ min($index + 1, 5) }}"
                     aria-label="{{ $offer->title }}">
                <a href="{{ route('offers.show', $offer->slug) }}" class="hp-ocard-img-link" tabindex="-1" aria-hidden="true">
                    @if($offer->cover_image)
                    <img src="{{ mediaUrl($offer->cover_image) }}"
                         alt="{{ $offer->title }}" class="hp-ocard-img"
                         loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async"
                         width="{{ $index === 0 ? '600' : '400' }}" height="{{ $index === 0 ? '800' : '300' }}">
                    @else
                    <div class="hp-ocard-img hp-ocard-img--placeholder" aria-hidden="true">
                        <i class="fas fa-image"></i>
                    </div>
                    @endif
                    <div class="hp-ocard-overlay" aria-hidden="true"></div>
                </a>
                @auth
                <button class="dt-wish-btn" data-wishlist-id="{{ $offer->id }}"
                        aria-label="Ajouter {{ $offer->title }} aux favoris"
                        aria-pressed="false" type="button">
                    <i class="far fa-heart" aria-hidden="true"></i>
                </button>
                @endauth
                <div class="hp-ocard-body">
                    @if($offer->category)
                        <p class="hp-ocard-cat">{{ $offer->category }}</p>
                    @endif
                    <h3 class="hp-ocard-title">
                        <a href="{{ route('offers.show', $offer->slug) }}">{{ $offer->title }}</a>
                    </h3>
                    @if($offer->city)
                        <p class="hp-ocard-city">
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            {{ $offer->city->name }}
                        </p>
                    @endif
                    <div class="hp-ocard-footer">
                        <div class="hp-ocard-price">
                            {{ number_format($offer->base_price, 0, ',', ' ') }}
                            <span class="hp-ocard-price-unit">FCFA / pers.</span>
                        </div>
                        @if($offer->reviews_count > 0)
                        <div class="hp-ocard-rating"
                             aria-label="Note : {{ number_format($offer->reviews_avg_rating, 1) }} sur 5">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            {{ number_format($offer->reviews_avg_rating, 1) }}
                            <span class="hp-ocard-rating-count">({{ $offer->reviews_count }})</span>
                        </div>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="hp-empty-state">
            <i class="fas fa-compass" aria-hidden="true"></i>
            <p>Les expériences arrivent bientôt. Revenez nous voir !</p>
            <a href="{{ route('offers.index') }}" class="dt-btn dt-btn--ambre">Explorer le catalogue</a>
        </div>
        @endif
    </div>
</section>

{{-- §6 DESTINATIONS --}}
<section class="hp-destinations" aria-labelledby="hp-dest-title">
    <div class="dt-container">
        <header class="hp-section-header">
            <div>
                <div class="hp-chip hp-chip--foret">
                    <i class="fas fa-map-marked-alt" aria-hidden="true"></i> Explorer
                </div>
                <h2 class="hp-section-title" id="hp-dest-title">
                    Choisissez votre<br><span class="hp-gradient-foret">destination</span>
                </h2>
            </div>
            <a href="{{ route('destinations') }}" class="hp-view-all hp-view-all--foret">
                Toutes les destinations <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </header>
        @if($featuredCities->isNotEmpty())
        <div class="hp-dest-grid">
            @foreach($featuredCities as $index => $city)
            <a href="{{ route('destinations.city', $city->slug) }}"
               class="hp-dest-card dt-reveal dt-delay-{{ $index + 1 }}"
               aria-label="{{ $city->name }}, {{ $city->offers_count }} {{ $city->offers_count > 1 ? 'expériences' : 'expérience' }}">
                @if($city->cover_image)
                <img src="{{ mediaUrl($city->cover_image) }}"
                     alt="{{ $city->name }}" class="hp-dest-img"
                     loading="lazy" decoding="async" width="400" height="533">
                @else
                <div class="hp-dest-img hp-dest-img--placeholder" aria-hidden="true">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                @endif
                <div class="hp-dest-overlay" aria-hidden="true"></div>
                <div class="hp-dest-body">
                    <h3 class="hp-dest-name">{{ $city->name }}</h3>
                    <p class="hp-dest-count">
                        <i class="fas fa-compass" aria-hidden="true"></i>
                        {{ $city->offers_count }} {{ $city->offers_count > 1 ? 'expériences' : 'expérience' }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- §7 POURQUOI --}}
<section class="hp-why" aria-labelledby="hp-why-title">
    <div class="hp-why-bg" aria-hidden="true"></div>
    <div class="dt-container hp-why-inner">
        <header class="hp-section-header hp-section-header--centered">
            <div class="hp-chip hp-chip--inv"><i class="fas fa-heart" aria-hidden="true"></i> Notre promesse</div>
            <h2 class="hp-section-title hp-section-title--inv" id="hp-why-title">
                Pourquoi choisir<br><span class="hp-gradient-ambre">DiscovTrip</span> ?
            </h2>
            <p class="hp-why-sub">
                Une agence locale, une promesse mondiale.
                Chaque détail pensé pour votre confort et votre découverte.
            </p>
        </header>
        @php
        $whyCards = [
            ['icon' => 'fa-map-marked-alt', 'color' => 'ambre',  'title' => 'Guides locaux certifiés',  'desc' => 'Des experts nés et formés au Bénin. Chaque guide connaît chaque sentier, chaque histoire, chaque saveur authentique de son territoire.'],
            ['icon' => 'fa-leaf',            'color' => 'foret',  'title' => 'Tourisme responsable',      'desc' => 'Nous investissons dans les communautés locales. Chaque réservation contribue directement aux familles d\'accueil et aux artisans.'],
            ['icon' => 'fa-shield-alt',      'color' => 'braise', 'title' => 'Sécurité & flexibilité',   'desc' => 'Annulation gratuite jusqu\'à 48h. Paiement sécurisé FedaPay & Stripe. Assistance locale 24/7 pendant tout votre séjour.'],
            ['icon' => 'fa-sliders-h',       'color' => 'ambre',  'title' => 'Expériences sur mesure',   'desc' => 'Durée, groupe, budget : chaque voyage s\'adapte à votre rythme. Nous personnalisons chaque détail pour une expérience unique.'],
        ];
        @endphp
        <div class="hp-why-grid">
            @foreach($whyCards as $index => $card)
            <div class="hp-why-card dt-reveal dt-delay-{{ $index + 1 }}">
                <div class="hp-why-icon hp-why-icon--{{ $card['color'] }}" aria-hidden="true">
                    <i class="fas {{ $card['icon'] }}"></i>
                </div>
                <h3 class="hp-why-card-title">{{ $card['title'] }}</h3>
                <p class="hp-why-card-desc">{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §8 PROCESS --}}
<section class="hp-process" aria-labelledby="hp-process-title">
    <div class="dt-container">
        <header class="hp-section-header hp-section-header--centered">
            <div class="hp-chip hp-chip--ambre">
                <i class="fas fa-route" aria-hidden="true"></i> Simple &amp; rapide
            </div>
            <h2 class="hp-section-title" id="hp-process-title">
                Réserver en <span class="hp-gradient-ambre">3 étapes</span>
            </h2>
        </header>
        @php
        $steps = [
            ['num' => '01', 'icon' => 'fa-search',           'title' => 'Choisissez', 'desc' => 'Parcourez notre catalogue. Filtrez par destination, durée ou type d\'activité pour trouver l\'expérience qui vous correspond.'],
            ['num' => '02', 'icon' => 'fa-calendar-check',   'title' => 'Réservez',   'desc' => 'Sélectionnez votre date, le nombre de participants et payez en toute sécurité avec FedaPay ou Stripe.'],
            ['num' => '03', 'icon' => 'fa-suitcase-rolling', 'title' => 'Vivez',      'desc' => 'Votre guide vous accueille sur place. Il ne vous reste plus qu\'à savourer chaque instant de votre aventure.'],
        ];
        @endphp
        <div class="hp-process-steps">
            <div class="hp-process-connector" aria-hidden="true"></div>
            @foreach($steps as $index => $step)
            <div class="hp-process-step dt-reveal dt-delay-{{ $index + 1 }}">
                <div class="hp-step-badge" aria-hidden="true">{{ $step['num'] }}</div>
                <div class="hp-step-icon-wrap" aria-hidden="true"><i class="fas {{ $step['icon'] }}"></i></div>
                <h3 class="hp-step-title">{{ $step['title'] }}</h3>
                <p class="hp-step-desc">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="hp-process-cta dt-reveal">
            <a href="{{ route('offers.index') }}" class="dt-btn dt-btn--ambre">
                <i class="fas fa-compass" aria-hidden="true"></i> Explorer les expériences
            </a>
        </div>
    </div>
</section>

{{-- §9 TÉMOIGNAGES
     CORRECTION : utilise $testimonials depuis la DB (HomeController)
     avec fallback sur des données statiques si la table est vide.
--}}
<section class="hp-testimonials" aria-labelledby="hp-testi-title">
    <div class="dt-container">
        <header class="hp-section-header">
            <div>
                <div class="hp-chip hp-chip--neutral">
                    <i class="fas fa-quote-left" aria-hidden="true"></i> Ils en parlent
                </div>
                <h2 class="hp-section-title" id="hp-testi-title">
                    Ce que disent<br>nos <span class="hp-gradient-ambre">voyageurs</span>
                </h2>
            </div>
        </header>

        @php
        // Fallback statique si la table testimonials est vide
        $fallbackTestimonials = [
            ['text' => 'Une expérience qui m\'a profondément transformé. Les guides connaissent chaque secret de ce pays magnifique. Je reviendrai sans la moindre hésitation.', 'client_name' => 'Marie Dupont',   'client_title' => 'Paris, France',       'rating' => 5],
            ['text' => 'Ganvié, c\'est un autre monde. DiscovTrip nous a offert un accès privilégié à des lieux et des familles qu\'aucune agence classique ne peut proposer.',   'client_name' => 'Kofi Mensah',    'client_title' => 'Accra, Ghana',        'rating' => 5],
            ['text' => 'Organisation parfaite, guides passionnés, lieux extraordinaires. Le Bénin m\'a surpris au-delà de toutes mes attentes. Un grand merci à toute l\'équipe !', 'client_name' => 'Sophie Laurent', 'client_title' => 'Bruxelles, Belgique',  'rating' => 5],
        ];
        $colors  = ['ambre','foret','braise'];
        $useDB   = $testimonials->isNotEmpty();
        $items   = $useDB ? $testimonials : collect($fallbackTestimonials);
        @endphp

        <div class="hp-testi-grid">
            @foreach($items as $index => $t)
            @php
                $text     = $useDB ? $t->testimonial : $t['text'];
                $name     = $useDB ? $t->client_name : $t['client_name'];
                $location = $useDB ? ($t->client_title ?? '') : $t['client_title'];
                $rating   = $useDB ? (int) $t->rating : $t['rating'];
                $initial  = strtoupper(substr($name, 0, 1));
                $color    = $colors[$index % 3];
            @endphp
            <blockquote class="hp-testi-card dt-reveal dt-delay-{{ $index + 1 }}"
                        aria-label="Témoignage de {{ $name }}">
                <div class="hp-testi-quote-mark" aria-hidden="true">"</div>
                <p class="hp-testi-text">{{ $text }}</p>
                <footer class="hp-testi-footer">
                    <div class="hp-testi-divider" aria-hidden="true"></div>
                    <div class="hp-testi-author">
                        @if($useDB && $t->client_photo)
                        <div class="hp-testi-avatar hp-testi-avatar--{{ $color }}" aria-hidden="true">
                            <img src="{{ mediaUrl($t->client_photo) }}"
                                 alt="{{ $name }}"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        </div>
                        @else
                        <div class="hp-testi-avatar hp-testi-avatar--{{ $color }}" aria-hidden="true">
                            {{ $initial }}
                        </div>
                        @endif
                        <div class="hp-testi-author-info">
                            <cite class="hp-testi-name">{{ $name }}</cite>
                            @if($location)
                            <p class="hp-testi-loc">
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                {{ $location }}
                            </p>
                            @endif
                            @if($useDB && $t->offer_title)
                            <p class="hp-testi-loc" style="color:var(--a-500);font-weight:600;">
                                <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                                {{ $t->offer_title }}
                            </p>
                            @endif
                            <div class="hp-testi-stars" aria-label="Note : {{ $rating }} sur 5">
                                @php
                                    $litStars = min((int)$rating, 5);
                                    echo str_repeat('<i class="fas fa-star" aria-hidden="true"></i>', $litStars);
                                @endphp
                            </div>
                        </div>
                    </div>
                </footer>
            </blockquote>
            @endforeach
        </div>
    </div>
</section>

{{-- §10 NEWSLETTER --}}
<section class="hp-newsletter" aria-labelledby="hp-newsletter-title">
    <div class="hp-newsletter-inner dt-container">
        <div class="hp-newsletter-text">
            <p class="hp-newsletter-eyebrow">Restez informé</p>
            <h2 class="hp-newsletter-title" id="hp-newsletter-title">
                Des expériences<br>rien que pour vous
            </h2>
            <p class="hp-newsletter-sub">
                Offres exclusives, nouvelles destinations, conseils de voyage — dans votre boîte mail.
                Pas de spam.
            </p>
        </div>
        <div class="hp-newsletter-form-wrap">
            <form class="hp-newsletter-form"
                  data-newsletter
                  action="{{ route('newsletter.subscribe') }}"
                  method="POST"
                  novalidate
                  aria-label="Formulaire newsletter">
                @csrf
                @if(session('newsletter_success'))
                <div style="padding:12px 16px;background:rgba(42,143,94,.1);border:1.5px solid rgba(42,143,94,.25);border-radius:10px;font-size:.85rem;color:var(--f-600);margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('newsletter_success') }}
                </div>
                @endif
                <div class="hp-newsletter-input-row">
                    <label for="nl-email" class="hp-newsletter-label">Votre adresse email</label>
                    <input type="email" id="nl-email" name="email"
                           class="hp-newsletter-input"
                           placeholder="votre@email.com"
                           required autocomplete="email" inputmode="email">
                    <button type="submit" class="hp-newsletter-btn">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        <span>S'inscrire</span>
                    </button>
                </div>
                <p class="hp-newsletter-privacy">
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    Données protégées — désabonnement en 1 clic
                </p>
            </form>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Counter animation ── */
    function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
    function runCounter(el) {
        var target = parseInt(el.getAttribute('data-target'), 10);
        var start  = null;
        (function tick(ts) {
            if (!start) start = ts;
            var p = Math.min((ts - start) / 1800, 1);
            el.textContent = Math.floor(easeOutCubic(p) * target);
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = target;
        }(performance.now()));
    }
    if ('IntersectionObserver' in window) {
        var co = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting && !e.target.dataset.done) {
                    e.target.dataset.done = '1';
                    runCounter(e.target);
                    co.unobserve(e.target);
                }
            });
        }, { threshold: 0.6 });
        document.querySelectorAll('.hp-stat-num[data-target]').forEach(function(el) {
            co.observe(el);
        });
    } else {
        document.querySelectorAll('.hp-stat-num[data-target]').forEach(function(el) {
            el.textContent = el.getAttribute('data-target');
        });
    }

    /* ── Parallaxe hero ── */
    var heroImg = document.getElementById('dt-hero-img');
    if (heroImg && window.matchMedia('(min-width: 1024px)').matches) {
        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    var sy = window.scrollY;
                    if (sy < window.innerHeight) {
                        heroImg.style.transform = 'translateY(' + (sy * 0.22) + 'px) scale(1.04)';
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /* ── Select has-value ── */
    document.querySelectorAll('.hp-search-select').forEach(function(sel) {
        function sync() { sel.classList.toggle('hp-search-select--filled', sel.value !== ''); }
        sel.addEventListener('change', sync);
        sync();
    });

})();
</script>
@endpush