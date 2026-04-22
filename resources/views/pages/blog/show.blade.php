@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' — Blog DiscovTrip')
@section('description', $post->meta_description ?: $post->excerpt)
@section('og_title', $post->title)
@section('og_description', $post->excerpt)
@section('og_image', $post->cover_image ? mediaUrl($post->cover_image) : asset('images/og-default.jpg'))

@push('styles')
    @vite('resources/css/pages/blog/show.css')
@endpush

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "BlogPosting",
            "headline": "{{ addslashes($post->title) }}",
            "description": "{{ addslashes($post->excerpt) }}",
            "image": "{{ $post->cover_image ? mediaUrl($post->cover_image) : asset('images/og-default.jpg') }}",
            "url": "{{ route('blog.show', $post->slug) }}",
            "datePublished": "{{ $post->published_at?->toIso8601String() }}",
            "dateModified": "{{ $post->updated_at->toIso8601String() }}",
            "inLanguage": "fr-FR",
            "author": {
                "@@type": "Person",
                "name": "{{ $post->author?->name ?? 'DiscovTrip' }}"
            },
            "publisher": {
                "@@type": "Organization",
                "name": "DiscovTrip",
                "url": "{{ config('app.url') }}",
                "logo": {
                    "@@type": "ImageObject",
                    "url": "{{ asset('images/logo.png') }}"
                }
            },
            "timeRequired": "PT{{ $post->reading_time }}M",
            "mainEntityOfPage": {
                "@@type": "WebPage",
                "@@id": "{{ route('blog.show', $post->slug) }}"
            }
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
                    "name": "Blog",
                    "item": "{{ route('blog.index') }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 3,
                    "name": "{{ addslashes(Str::limit($post->title, 60)) }}",
                    "item": "{{ route('blog.show', $post->slug) }}"
                }
            ]
        }
    ]
}
</script>
@endpush

@section('content')

{{-- Breadcrumb ────────────────────────────────────────── --}}
<nav class="blog-breadcrumb" aria-label="Fil d'Ariane">
    <a href="{{ route('home') }}">Accueil</a>
    <span class="blog-breadcrumb__sep">›</span>
    <a href="{{ route('blog.index') }}">Blog</a>
    <span class="blog-breadcrumb__sep">›</span>
    <span>{{ Str::limit($post->title, 50) }}</span>
</nav>

{{-- Hero ─────────────────────────────────────────────── --}}
@if($post->cover_image)
<div class="blog-post-hero">
    <img src="{{ mediaUrl($post->cover_image) }}"
         alt="{{ $post->title }}"
         class="blog-post-hero__img"
         loading="eager">
    <div class="blog-post-hero__overlay" aria-hidden="true"></div>
    <div class="blog-post-hero__content">
        <span class="blog-post-hero__cat"
              style="background:{{ $post->category_color }}">{{ $post->category_label }}</span>
        <h1 class="blog-post-hero__title">{{ $post->title }}</h1>
        <div class="blog-post-hero__meta">
            <span><i class="fas fa-user" aria-hidden="true"></i> {{ $post->author?->name ?? 'DiscovTrip' }}</span>
            <span><i class="fas fa-calendar" aria-hidden="true"></i> {{ $post->published_at?->format('d M Y') }}</span>
            <span><i class="fas fa-clock" aria-hidden="true"></i> {{ $post->reading_time_formatted }}</span>
        </div>
    </div>
</div>
@else
<div class="blog-post-hero--placeholder">
    <div>
        <span class="blog-post-hero__cat"
              style="background:{{ $post->category_color }}">{{ $post->category_label }}</span>
        <h1 class="blog-post-hero__title">{{ $post->title }}</h1>
        <div class="blog-post-hero__meta">
            <span><i class="fas fa-user" aria-hidden="true"></i> {{ $post->author?->name ?? 'DiscovTrip' }}</span>
            <span><i class="fas fa-calendar" aria-hidden="true"></i> {{ $post->published_at?->format('d M Y') }}</span>
            <span><i class="fas fa-clock" aria-hidden="true"></i> {{ $post->reading_time_formatted }}</span>
        </div>
    </div>
</div>
@endif

{{-- Corps ────────────────────────────────────────────── --}}
<div class="blog-post-body">

    <article>
        <div class="blog-post-content">
            {!! $post->content !!}
        </div>

        @if($post->tags && count($post->tags) > 0)
        <div class="blog-post-tags" aria-label="Tags">
            @foreach($post->tags as $tag)
                <span class="blog-post-tag">#{{ $tag }}</span>
            @endforeach
        </div>
        @endif

        @if($suggestedOffers->isNotEmpty())
        <div class="blog-suggested">
            <h3 class="blog-suggested__title">Prêt à vivre l'expérience ?</h3>
            <p class="blog-suggested__sub">Réservez directement avec nos guides locaux certifiés.</p>
            <div class="blog-suggested__grid">
                @foreach($suggestedOffers as $offer)
                <a href="{{ route('offers.show', $offer->slug) }}" class="blog-suggested__card">
                    @if($offer->cover_image)
                        <img src="{{ mediaUrl($offer->cover_image) }}"
                             alt="{{ $offer->title }}"
                             class="blog-suggested__card-img">
                    @else
                        <div class="blog-suggested__card-ph" aria-hidden="true">✨</div>
                    @endif
                    <div class="blog-suggested__card-body">
                        <p class="blog-suggested__card-title">{{ Str::limit($offer->title, 40) }}</p>
                        <p class="blog-suggested__card-price">
                            À partir de {{ number_format($offer->effective_price, 0, '', ' ') }} FCFA
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </article>

    {{-- Sidebar ──────────────────────────────────────── --}}
    <aside class="blog-post-sidebar" aria-label="Sidebar article">
        <div class="blog-post-sidebar__about">
            <img src="{{ asset('images/logo.png') }}" alt="DiscovTrip" class="blog-post-sidebar__logo">
            <p class="blog-post-sidebar__text">
                Plateforme de tourisme authentique au Bénin. Nous connectons les voyageurs curieux
                aux gardiens du savoir local.
            </p>
            <a href="{{ route('offers.index') }}" class="blog-post-sidebar__btn">
                Voir les expériences →
            </a>
        </div>

        <div class="blog-post-sidebar__share">
            <p class="blog-post-sidebar__share-title">Partager cet article</p>
            <div class="blog-post-sidebar__share-btns">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="blog-post-sidebar__share-btn blog-post-sidebar__share-btn--fb"
                   aria-label="Partager sur Facebook">
                    <i class="fab fa-facebook-f" aria-hidden="true"></i> Facebook
                </a>
                <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . route('blog.show', $post->slug)) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="blog-post-sidebar__share-btn blog-post-sidebar__share-btn--wa"
                   aria-label="Partager sur WhatsApp">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp
                </a>
            </div>
        </div>
    </aside>
</div>

{{-- Articles liés ────────────────────────────────────── --}}
@if($related->isNotEmpty())
<section class="blog-related" aria-label="Articles similaires">
    <h2 class="blog-related__title">Articles similaires</h2>
    <div class="blog-related__grid">
        @foreach($related as $item)
        <div class="blog-related__card">
            <a href="{{ route('blog.show', $item->slug) }}" tabindex="-1" aria-hidden="true">
                @if($item->cover_image)
                    <img src="{{ mediaUrl($item->cover_image) }}"
                         alt="{{ $item->title }}"
                         class="blog-related__card-img"
                         loading="lazy">
                @else
                    <div class="blog-related__card-ph" aria-hidden="true">
                        {{ match($item->category) { 'destinations' => '🗺️', 'culture' => '🎭', 'pratique' => '📋', default => '💡' } }}
                    </div>
                @endif
            </a>
            <div class="blog-related__card-body">
                <a href="{{ route('blog.show', $item->slug) }}" class="blog-related__card-title">
                    {{ $item->title }}
                </a>
                <p class="blog-related__card-meta">{{ $item->reading_time_formatted }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

@endsection