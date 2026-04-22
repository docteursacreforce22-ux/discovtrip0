@extends('layouts.app')

@section('title', 'Questions Fréquentes — DiscovTrip')

@push('meta')
<meta name="description" content="Réponses à toutes vos questions sur DiscovTrip : réservations, paiements, annulations, guides certifiés et expériences au Bénin.">
@endpush

@push('styles')
    @vite('resources/css/pages/faq.css')
@endpush

@section('content')

{{-- ════════════════════════════════════════════
     §1  HERO + Recherche
════════════════════════════════════════════ --}}
<section class="fq-hero">
    <div class="fq-hero-glow" aria-hidden="true"></div>

    <div class="dt-container fq-hero-inner">
        <div class="fq-hero-eyebrow">
            <span aria-hidden="true"></span>
            Centre d'aide
        </div>
        <h1 class="fq-hero-title">
            Toutes vos <em>questions</em>,<br>toutes les réponses
        </h1>
        <p class="fq-hero-sub">
            {{ $totalFaqs }} réponses disponibles. Trouvez la vôtre en quelques secondes.
        </p>

        <div class="fq-search">
            <i class="fas fa-search fq-search-icon" aria-hidden="true"></i>
            <input type="search" id="fq-search-input" class="fq-search-input"
                   placeholder="Rechercher dans la FAQ… ex: annulation, paiement, guide"
                   aria-label="Rechercher dans la FAQ"
                   autocomplete="off">
            <span class="fq-search-clear" id="fq-search-clear" role="button" aria-label="Effacer">
                <i class="fas fa-times"></i>
            </span>
        </div>
    </div>

    <div class="fq-hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 56" preserveAspectRatio="none">
            <path d="M0,28 C360,56 1080,0 1440,28 L1440,56 L0,56 Z" fill="var(--cream)"/>
        </svg>
    </div>
</section>

{{-- ════════════════════════════════════════════
     §2  FILTRES CATÉGORIES
════════════════════════════════════════════ --}}
<div class="fq-filters" id="fq-filters">
    <div class="dt-container fq-filters-inner">
        <button class="fq-cat-btn fq-cat-btn--active" data-cat="all">
            Toutes <span class="fq-cat-count">{{ $totalFaqs }}</span>
        </button>
        @foreach($faqs as $key => $group)
        <button class="fq-cat-btn" data-cat="{{ $key }}">
            <i class="fas fa-{{ $group['icon'] }}" aria-hidden="true"></i>
            {{ $group['label'] }}
            <span class="fq-cat-count">{{ count($group['items']) }}</span>
        </button>
        @endforeach
    </div>
</div>

{{-- ════════════════════════════════════════════
     §3  CONTENU
════════════════════════════════════════════ --}}
<section class="fq-body">
    <div class="dt-container fq-layout">

        {{-- Groupes FAQ --}}
        <div class="fq-groups" id="fq-groups">

            {{-- No results --}}
            <div class="fq-no-results" id="fq-no-results">
                <div class="fq-no-results-icon">🔍</div>
                <p>Aucune réponse ne correspond à votre recherche.</p>
                <button class="fq-clear-btn" id="fq-clear-btn">Effacer la recherche</button>
            </div>

            @foreach($faqs as $key => $group)
            <div class="fq-group" data-group="{{ $key }}">
                <div class="fq-group-title">
                    <span class="fq-group-title-bar"></span>
                    <i class="fas fa-{{ $group['icon'] }}" aria-hidden="true"></i>
                    {{ $group['label'] }}
                </div>

                @foreach($group['items'] as $i => $faq)
                <details class="fq-item"
                         data-q="{{ strtolower($faq['q']) }}"
                         data-a="{{ strtolower(strip_tags($faq['a'])) }}"
                         data-group="{{ $key }}"
                         {{ $i === 0 && $key === array_key_first($faqs) ? 'open' : '' }}>
                    <summary class="fq-q">
                        <span>{{ $faq['q'] }}</span>
                        <span class="fq-q-icon" aria-hidden="true">
                            <i class="fas fa-plus"></i>
                        </span>
                    </summary>
                    <p class="fq-a">{!! $faq['a'] !!}</p>
                </details>
                @endforeach
            </div>
            @endforeach

        </div>

        {{-- Sidebar --}}
        <aside class="fq-sidebar">

            {{-- Stats --}}
            <div class="fq-sidebar-card">
                <div class="fq-sidebar-head">
                    <i class="fas fa-chart-bar"></i> En chiffres
                </div>
                <div class="fq-sidebar-body">
                    <div class="fq-stat">
                        @foreach($faqs as $key => $group)
                        <div class="fq-stat-item">
                            <span class="fq-stat-lbl">{{ $group['label'] }}</span>
                            <span class="fq-stat-val">{{ count($group['items']) }} réponses</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- WhatsApp --}}
            <a href="https://wa.me/22901000000"
               target="_blank" rel="noopener noreferrer"
               class="fq-whatsapp">
                <div class="fq-whatsapp-icon">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                </div>
                <div class="fq-whatsapp-title">Vous ne trouvez pas ?</div>
                <div class="fq-whatsapp-sub">Discutez avec notre équipe sur WhatsApp</div>
                <div class="fq-whatsapp-btn">
                    Nous écrire <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            {{-- Liens légaux --}}
            <div class="fq-sidebar-card">
                <div class="fq-sidebar-head">
                    <i class="fas fa-file-alt"></i> Documents utiles
                </div>
                <div class="fq-legal-links">
                    <a href="{{ route('cancellation') }}" class="fq-legal-link">
                        <span><i class="fas fa-undo" style="color:var(--a-500);margin-right:8px"></i> Politique d'annulation</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('cgu') }}" class="fq-legal-link">
                        <span><i class="fas fa-file-contract" style="color:var(--a-500);margin-right:8px"></i> Conditions d'utilisation</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('privacy') }}" class="fq-legal-link">
                        <span><i class="fas fa-shield-alt" style="color:var(--a-500);margin-right:8px"></i> Confidentialité</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="fq-legal-link">
                        <span><i class="fas fa-headset" style="color:var(--a-500);margin-right:8px"></i> Contacter le support</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </aside>
    </div>
</section>

{{-- ════════════════════════════════════════════
     §4  CTA
════════════════════════════════════════════ --}}
<section class="fq-cta">
    <div class="fq-cta-glow" aria-hidden="true"></div>
    <div class="dt-container fq-cta-inner">
        <h2 class="fq-cta-title">Toujours des <em>questions</em> ?</h2>
        <p class="fq-cta-sub">Notre équipe locale au Bénin vous répond sous 24h, du lundi au samedi.</p>
        <div class="fq-cta-btns">
            <a href="{{ route('contact') }}" class="dt-btn dt-btn--ambre">
                <i class="fas fa-headset"></i> Contacter l'équipe
            </a>
            <a href="{{ route('offers.index') }}" class="dt-btn dt-btn--ghost-dark">
                <i class="fas fa-ticket-alt"></i> Voir les expériences
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {

    const searchInput  = document.getElementById('fq-search-input');
    const searchClear  = document.getElementById('fq-search-clear');
    const clearBtn     = document.getElementById('fq-clear-btn');
    const noResults    = document.getElementById('fq-no-results');
    const allItems     = document.querySelectorAll('.fq-item');
    const allGroups    = document.querySelectorAll('.fq-group');
    const catBtns      = document.querySelectorAll('.fq-cat-btn');
    const filtersEl    = document.getElementById('fq-filters');

    let activeCategory = 'all';

    /* ── Filtres sticky shadow ── */
    window.addEventListener('scroll', () => {
        filtersEl?.classList.toggle('fq-filters--scrolled', window.scrollY > 200);
    }, { passive: true });

    /* ── Accordion : un seul ouvert à la fois ── */
    allItems.forEach(item => {
        item.addEventListener('toggle', () => {
            if (item.open) {
                allItems.forEach(other => {
                    if (other !== item && other.open) other.removeAttribute('open');
                });
            }
        });
    });

    /* ── Filtres catégorie ── */
    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catBtns.forEach(b => b.classList.remove('fq-cat-btn--active'));
            btn.classList.add('fq-cat-btn--active');
            activeCategory = btn.dataset.cat;

            // Reset recherche
            if (searchInput) searchInput.value = '';
            if (searchClear) { searchClear.style.opacity = '0'; searchClear.style.pointerEvents = 'none'; }

            applyFilters('');
        });
    });

    /* ── Recherche ── */
    const applyFilters = (query) => {
        const q = query.trim().toLowerCase();

        // Toggle clear button
        if (searchClear) {
            searchClear.style.opacity = q ? '1' : '0';
            searchClear.style.pointerEvents = q ? 'auto' : 'none';
        }

        let visible = 0;

        allGroups.forEach(group => {
            const groupCat   = group.dataset.group;
            const catMatch   = activeCategory === 'all' || activeCategory === groupCat;
            const groupItems = group.querySelectorAll('.fq-item');
            let groupVisible = 0;

            groupItems.forEach(item => {
                const qText = item.dataset.q || '';
                const aText = item.dataset.a || '';
                const textMatch = !q || qText.includes(q) || aText.includes(q);
                const show = catMatch && textMatch;

                item.style.display = show ? '' : 'none';
                if (show) groupVisible++;
            });

            group.style.display = groupVisible > 0 ? '' : 'none';
            visible += groupVisible;
        });

        noResults.style.display = visible === 0 ? 'flex' : 'none';
    };

    let debounce;
    searchInput?.addEventListener('input', e => {
        clearTimeout(debounce);
        debounce = setTimeout(() => applyFilters(e.target.value), 200);
    });

    const clearSearch = () => {
        if (searchInput) searchInput.value = '';
        applyFilters('');
    };
    searchClear?.addEventListener('click', clearSearch);
    clearBtn?.addEventListener('click', clearSearch);

})();
</script>
@endpush