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

$rCount     = (int)($offer->reviews_count ?? 0);
$hasReviews = $rCount >= 5;
$isUrgent   = ($offer->available_spots ?? 0) > 0 && $offer->available_spots <= 5;
$wishlisted = in_array($offer->id, $wishlistIds ?? []);
$isPromo    = $isPromo ?? ($offer->is_promo && $offer->promotional_price);
$discount   = $isPromo ? round((1 - $offer->promotional_price / $offer->base_price) * 100) : 0;
@endphp

<article class="opl-card {{ $isPromo ? 'opl-card-promo' : '' }}">

    {{-- Ribbon promo --}}
    @if($isPromo)
    <div class="opl-promo-ribbon">−{{ $discount }}%</div>
    @endif

    {{-- Wishlist --}}
    @auth
    <button class="opl-wish-btn wishlist-btn {{ $wishlisted ? 'active' : '' }}"
            data-url="{{ route('wishlist.toggle') }}"
            data-offer-id="{{ $offer->id }}"
            data-wishlisted="{{ $wishlisted ? '1' : '0' }}"
            title="{{ $wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}">
        <i class="{{ $wishlisted ? 'fas' : 'far' }} fa-heart"></i>
    </button>
    @else
    <a href="{{ route('login') }}" class="opl-wish-btn" title="Connectez-vous pour sauvegarder">
        <i class="far fa-heart"></i>
    </a>
    @endauth

    {{-- Image --}}
    <a href="{{ route('offers.show', $offer->slug) }}" class="opl-card-img-wrap">
        @if($offer->cover_image)
            <img src="{{ asset('storage/'.$offer->cover_image) }}"
                 alt="{{ $offer->title }}"
                 class="opl-card-img"
                 loading="lazy">
        @else
            @php $gr = $gradients[$offer->category] ?? ['#D4E8C8','#8BBF6E']; @endphp
            <div class="opl-card-img opl-card-img-ph"
                 style="background:linear-gradient(135deg,{{ $gr[0] }},{{ $gr[1] }});">
                <span>{{ $emojis[$offer->category] ?? '✨' }}</span>
            </div>
        @endif
        <div class="opl-card-img-overlay"></div>

        <div class="opl-card-badges">
            @if($offer->is_featured)
                <span class="opl-badge opl-badge-featured">⭐ Coup de cœur</span>
            @elseif($rCount < 5)
                <span class="opl-badge opl-badge-new">✨ Nouveau</span>
            @endif
            @if($offer->is_instant_booking)
                <span class="opl-badge opl-badge-instant">⚡</span>
            @endif
        </div>

        @if($isUrgent)
        <div class="opl-urgency">
            <i class="fas fa-fire"></i>
            {{ $offer->available_spots }} place{{ $offer->available_spots > 1 ? 's' : '' }}
        </div>
        @endif
    </a>

    {{-- Corps --}}
    <div class="opl-card-body">
        <div class="opl-card-location">
            <i class="fas fa-map-marker-alt"></i>
            {{ $offer->city->name }} · <span>{{ $offer->category_label }}</span>
        </div>

        <h3 class="opl-card-title">
            <a href="{{ route('offers.show', $offer->slug) }}">{{ $offer->title }}</a>
        </h3>

        <div class="opl-card-meta">
            @if($hasReviews)
                <div class="opl-card-rating">
                    <span class="opl-stars">
                        @php
                        $litC = min((int)round($offer->average_rating ?? 0), 5);
                        echo str_repeat('<i class="fas fa-star lit"></i>', $litC) . str_repeat('<i class="fas fa-star"></i>', 5 - $litC);
                    @endphp
                    </span>
                    <strong>{{ number_format($offer->average_rating ?? 0, 1) }}</strong>
                    <span>({{ $rCount }})</span>
                </div>
            @else
                <span class="opl-new-tag">✨ Nouveau partenaire</span>
            @endif
            <div class="opl-card-duration">
                <i class="fas fa-clock"></i>
                {{ floor($offer->duration_minutes/60) }}h{{ $offer->duration_minutes%60>0?($offer->duration_minutes%60).'min':'' }}
            </div>
        </div>

        <div class="opl-card-footer">
            <div class="opl-card-price-block">
                <span class="opl-price-from">À partir de</span>
                @if($isPromo)
                    <div class="opl-price-promo-wrap">
                        <span class="opl-price-old">{{ number_format($offer->base_price, 0, '', ' ') }}</span>
                        <div class="opl-price-new">
                            {{ number_format($offer->promotional_price, 0, '', ' ') }}
                            <span class="opl-currency">FCFA</span>
                        </div>
                    </div>
                @else
                    <div class="opl-price">
                        {{ number_format($offer->base_price, 0, '', ' ') }}
                        <span class="opl-currency">FCFA</span>
                    </div>
                @endif
                <div class="opl-price-eur">
                    ≈ {{ number_format(($isPromo ? $offer->promotional_price : $offer->base_price) / config('discovtrip.eur_rate', 655.957), 0, '', ' ') }} €
                </div>
            </div>
            <a href="{{ route('offers.show', $offer->slug) }}" class="opl-card-cta">
                Réserver <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</article>