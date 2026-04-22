@extends('layouts.app')

@section('title', 'Blog — Voyager au Bénin | DiscovTrip')
@section('description', 'Conseils voyage, guides des destinations, culture béninoise : tout ce qu\'il faut savoir avant de partir au Bénin avec DiscovTrip.')
@section('og_title', 'Blog DiscovTrip — Le magazine du voyage au Bénin')
@section('og_description', 'Guides pratiques, récits de destination et conseils d\'experts locaux pour préparer votre séjour au Bénin.')

@push('styles')
    @vite('resources/css/pages/blog/index.css')
@endpush

@section('content')

{{-- §1 HERO ──────────────────────────────────────────────── --}}
<section class="blog-hero" aria-label="En-tête blog">
    {{-- Fond : image DB ou var(--f-900) + motif wax --}}
    <x-hero-bg setting-key="hero_blog" pattern-id="wp-blog" />

    <div class="dt-container" style="position:relative;z-index:2">
        <p class="blog-hero__eyebrow">✦ Le magazine DiscovTrip</p>
        <h1 class="blog-hero__title">Voyager au Bénin,<br><em>ça se prépare</em></h1>
        <p class="blog-hero__sub">
            Guides pratiques, récits de destination, culture et conseils d'experts locaux
            pour préparer votre séjour.
        </p>

        <nav class="blog-cats" aria-label="Filtrer par catégorie">
            <a href="{{ route('blog.index') }}"
               class="blog-cat-btn {{ !$category ? 'blog-cat-btn--active' : '' }}">
                Tous
            </a>
            @foreach([
                'destinations' => '🗺️ Destinations',
                'conseils'     => '💡 Conseils',
                'culture'      => '🎭 Culture',
                'pratique'     => '📋 Pratique',
            ] as $key => $label)
            <a href="{{ route('blog.index', ['category' => $key]) }}"
               class="blog-cat-btn {{ $category === $key ? 'blog-cat-btn--active' : '' }}">
                {{ $label }}
            </a>
            @endforeach
        </nav>
    </div>
</section>

{{-- §2 CONTENU ───────────────────────────────────────────── --}}
<div class="blog-main">

    <main id="main-content">
        <div class="blog-grid">
            @forelse($posts as $post)
            <article class="blog-card">

                <a href="{{ route('blog.show', $post->slug) }}" tabindex="-1" aria-hidden="true">
                    @if($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}"
                             alt="{{ $post->title }}"
                             class="blog-card__img"
                             loading="lazy">
                    @else
                        <div class="blog-card__img-placeholder" aria-hidden="true">
                            {{ match($post->category) {
                                'destinations' => '🗺️', 'culture' => '🎭',
                                'pratique' => '📋', default => '💡'
                            } }}
                        </div>
                    @endif
                </a>

                <div class="blog-card__body">
                    <span class="blog-card__cat"
                          style="background:{{ $post->category_color }}22;color:{{ $post->category_color }}">
                        {{ $post->category_label }}
                    </span>
                    <h2 class="blog-card__title">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    <p class="blog-card__excerpt">{{ $post->excerpt }}</p>
                    <div class="blog-card__footer">
                        <span>
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            {{ $post->reading_time_formatted }}
                        </span>
                        <a href="{{ route('blog.show', $post->slug) }}" class="blog-card__cta">
                            Lire <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </article>
            @empty
            <div class="blog-empty">
                <h3 class="blog-empty__title">Les premiers articles arrivent bientôt !</h3>
                <p>Notre équipe rédige des guides complets sur le Bénin.</p>
            </div>
            @endforelse
        </div>

        <div class="blog-pagination">{{ $posts->links() }}</div>
    </main>

    <aside class="blog-sidebar" aria-label="Sidebar">
        @if($recentPosts->isNotEmpty())
        <h2 class="blog-sidebar__heading">Articles récents</h2>
        @foreach($recentPosts as $recent)
        <div class="blog-recent-item">
            @if($recent->cover_image)
                <img src="{{ asset('storage/'.$recent->cover_image) }}"
                     alt="{{ $recent->title }}"
                     class="blog-recent-item__img"
                     loading="lazy">
            @else
                <div class="blog-recent-item__img-ph" aria-hidden="true">
                    {{ match($recent->category) { 'destinations' => '🗺️', 'culture' => '🎭', 'pratique' => '📋', default => '💡' } }}
                </div>
            @endif
            <div>
                <a href="{{ route('blog.show', $recent->slug) }}" class="blog-recent-item__title">
                    {{ $recent->title }}
                </a>
                <p class="blog-recent-item__date">{{ $recent->published_at?->format('d M Y') }}</p>
            </div>
        </div>
        @endforeach
        @endif

        <div class="blog-sidebar-cta">
            <h3 class="blog-sidebar-cta__title">Prêt à partir ?</h3>
            <p class="blog-sidebar-cta__text">Nos guides locaux vous attendent au Bénin.</p>
            <a href="{{ route('offers.index') }}" class="blog-sidebar-cta__btn">
                Voir les expériences
            </a>
        </div>
    </aside>
</div>

@endsection