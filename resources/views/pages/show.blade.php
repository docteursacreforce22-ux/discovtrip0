@extends('layouts.app')
@section('title', $offer->title . ' | DiscovTrip')

@section('content')

@php $wishlisted = Auth::check() && Auth::user()->hasWishlisted($offer->id); @endphp

{{-- ── BREADCRUMB ── --}}
<div style="background:var(--ivory);border-bottom:1px solid var(--border-light);padding:14px 48px;">
    <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-soft);">
        <a href="{{ route('home') }}"       style="color:var(--text-soft);text-decoration:none;transition:color 0.2s;" onmouseenter="this.style.color='var(--copper)'" onmouseleave="this.style.color='var(--text-soft)'">Accueil</a>
        <span style="color:var(--border);">›</span>
        <a href="{{ route('offers.index') }}" style="color:var(--text-soft);text-decoration:none;transition:color 0.2s;" onmouseenter="this.style.color='var(--copper)'" onmouseleave="this.style.color='var(--text-soft)'">Expériences</a>
        <span style="color:var(--border);">›</span>
        <span style="color:var(--text-primary);font-weight:600;">{{ Str::limit($offer->title, 40) }}</span>
    </div>
</div>

{{-- ── HERO IMAGE ── --}}
<div style="height:480px;background:linear-gradient(135deg,#2C1E0A,#1A1208);position:relative;overflow:hidden;">
    @if($offer->cover_image)
    <img src="{{ asset('storage/'.$offer->cover_image) }}" alt="{{ $offer->title }}"
         style="width:100%;height:100%;object-fit:cover;opacity:.85;">
    @endif
    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(13,10,5,.8) 0%,rgba(13,10,5,.2) 50%,transparent 100%);"></div>

    {{-- Actions flottantes --}}
    <div style="position:absolute;top:20px;right:20px;display:flex;gap:10px;">
        {{-- Wishlist --}}
        @auth
        <button id="wishlist-btn"
                data-offer-id="{{ $offer->id }}"
                data-url="{{ route('wishlist.toggle', $offer) }}"
                data-wishlisted="{{ $wishlisted ? '1' : '0' }}"
                style="width:44px;height:44px;border-radius:50%;border:none;cursor:pointer;background:{{ $wishlisted ? 'rgba(193,68,14,.9)' : 'rgba(26,18,8,.65)' }};backdrop-filter:blur(12px);display:flex;align-items:center;justify-content:center;transition:all 0.25s;box-shadow:0 4px 12px rgba(0,0,0,.3);"
                title="{{ $wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                onmouseenter="this.style.transform='scale(1.1)'" onmouseleave="this.style.transform=''">
            <i id="wishlist-icon" class="{{ $wishlisted ? 'fas' : 'far' }} fa-heart" style="font-size:16px;color:white;"></i>
        </button>
        @else
        <a href="{{ route('login') }}" style="width:44px;height:44px;border-radius:50%;background:rgba(26,18,8,.65);backdrop-filter:blur(12px);display:flex;align-items:center;justify-content:center;transition:all 0.25s;" title="Connectez-vous pour sauvegarder">
            <i class="far fa-heart" style="font-size:16px;color:rgba(255,255,255,.7);"></i>
        </a>
        @endauth

        {{-- Partager --}}
        <button onclick="navigator.share ? navigator.share({title:'{{ $offer->title }}',url:window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                style="width:44px;height:44px;border-radius:50%;border:none;cursor:pointer;background:rgba(26,18,8,.65);backdrop-filter:blur(12px);display:flex;align-items:center;justify-content:center;transition:all 0.25s;"
                title="Partager"
                onmouseenter="this.style.transform='scale(1.1)'" onmouseleave="this.style.transform=''">
            <i class="fas fa-share-alt" style="font-size:14px;color:rgba(255,255,255,.8);"></i>
        </button>
    </div>

    {{-- Titre sur l'image --}}
    <div style="position:absolute;bottom:0;left:0;right:0;padding:40px 48px;">
        <div style="max-width:1280px;margin:0 auto;">
            @if($offer->category)
            <div style="display:inline-flex;padding:4px 12px;border-radius:100px;background:rgba(184,117,26,.25);border:1px solid rgba(212,146,77,.3);font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--copper-light);margin-bottom:12px;">
                {{ ucfirst($offer->category) }}
            </div>
            @endif
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(28px,4vw,48px);font-weight:900;color:var(--ivory-alt);line-height:1.1;margin-bottom:12px;">
                {{ $offer->title }}
            </h1>
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                @if($offer->city)
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:rgba(247,242,236,.7);">
                    <i class="fas fa-map-marker-alt" style="color:var(--copper);font-size:11px;"></i>
                    {{ $offer->city->name }}
                </div>
                @endif
                @if($offer->duration_hours)
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:rgba(247,242,236,.7);">
                    <i class="fas fa-clock" style="color:var(--copper);font-size:11px;"></i>
                    {{ $offer->duration_hours }}h
                </div>
                @endif
                @if($offer->reviews_avg_rating)
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:rgba(247,242,236,.7);">
                    <span style="color:#D4924D;">★</span>
                    <strong style="color:var(--ivory-alt);">{{ number_format($offer->reviews_avg_rating,1) }}</strong>
                    <span>({{ $offer->reviews_count }} avis)</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── CONTENU + SIDEBAR ── --}}
<div style="background:var(--ivory-alt);padding:48px;">
    <div style="max-width:1280px;margin:0 auto;display:grid;grid-template-columns:1fr 380px;gap:40px;align-items:start;">

        {{-- ── COLONNE GAUCHE ── --}}
        <div style="display:flex;flex-direction:column;gap:28px;">

            {{-- Description --}}
            <div style="background:var(--ivory);border:1.5px solid var(--border-light);border-radius:20px;padding:36px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:20px;">
                    À propos de cette expérience
                </h2>
                <div style="font-size:14px;line-height:1.85;color:var(--text-mid);">
                    {!! nl2br(e($offer->description)) !!}
                </div>
            </div>

            {{-- Points forts --}}
            @if($offer->highlights)
            <div style="background:var(--ivory);border:1.5px solid var(--border-light);border-radius:20px;padding:36px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:20px;">
                    Points forts
                </h2>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach(explode("\n", $offer->highlights) as $h)
                    @if(trim($h))
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:22px;height:22px;border-radius:50%;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <i class="fas fa-check" style="font-size:9px;color:#16a34a;"></i>
                        </div>
                        <span style="font-size:14px;line-height:1.5;color:var(--text-mid);">{{ trim($h) }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Ce qui est inclus --}}
            @if($offer->included)
            <div style="background:var(--ivory);border:1.5px solid var(--border-light);border-radius:20px;padding:36px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:20px;">
                    Ce qui est inclus
                </h2>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    @foreach(explode("\n", $offer->included) as $item)
                    @if(trim($item))
                    <div style="display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-circle-check" style="font-size:14px;color:var(--copper);flex-shrink:0;"></i>
                        <span style="font-size:13px;color:var(--text-mid);">{{ trim($item) }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Avis --}}
            <div style="background:var(--ivory);border:1.5px solid var(--border-light);border-radius:20px;padding:36px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:24px;">
                    Avis clients
                    <span style="font-size:15px;font-weight:400;color:var(--text-soft);margin-left:8px;">({{ $offer->reviews_count ?? 0 }})</span>
                </h2>

                @forelse($offer->reviews ?? [] as $review)
                <div style="padding:20px 0;border-bottom:1px solid var(--border-light);">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,rgba(212,146,77,.2),rgba(184,117,26,.1));border:1.5px solid rgba(184,117,26,.2);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--copper);flex-shrink:0;">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;font-size:14px;color:var(--text-primary);">{{ $review->user->name ?? 'Voyageur' }}</div>
                            <div style="font-size:12px;color:var(--copper);">
                                @php echo str_repeat('★', 5); @endphp
                            </div>
                        </div>
                        <span style="font-size:11px;color:var(--text-soft);">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <p style="font-size:14px;line-height:1.7;color:var(--text-mid);">{{ $review->comment }}</p>
                </div>
                @empty
                <div style="text-align:center;padding:40px 0;">
                    <div style="font-size:36px;margin-bottom:12px;">✨</div>
                    <p style="font-size:14px;color:var(--text-mid);">Aucun avis pour le moment. Soyez le premier !</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── SIDEBAR RÉSERVATION ── --}}
        <div style="position:sticky;top:100px;">
            <div style="background:var(--ivory);border:1.5px solid var(--border-light);border-radius:20px;padding:32px;box-shadow:0 16px 40px rgba(26,18,8,.08);">

                {{-- Prix --}}
                <div style="margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border-light);">
                    <span style="font-size:11px;color:var(--text-soft);">À partir de</span>
                    <div style="font-family:'Playfair Display',serif;font-size:36px;font-weight:900;color:var(--copper);line-height:1.1;">
                        {{ number_format($offer->base_price ?? 0, 0, ',', ' ') }}
                        <span style="font-size:16px;font-weight:400;color:var(--text-soft);">FCFA</span>
                    </div>
                    <div style="font-size:12px;color:var(--text-soft);">par personne</div>
                </div>

                {{-- Formulaire --}}
                <form action="{{ route('bookings.store') }}" method="POST" x-data="bookingForm()" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf
                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">

                    {{-- Date --}}
                    <div>
                        <label style="display:block;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-mid);margin-bottom:7px;">Date</label>
                        <input type="date" name="date" x-model="date" min="{{ date('Y-m-d') }}" required
                               style="width:100%;padding:11px 14px;border-radius:10px;box-sizing:border-box;border:1.5px solid var(--border);background:var(--ivory-alt);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text-primary);outline:none;transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--copper)'" onblur="this.style.borderColor='var(--border)'">
                    </div>

                    {{-- Participants --}}
                    <div>
                        <label style="display:block;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-mid);margin-bottom:7px;">Participants</label>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <button type="button" @click="participants = Math.max(1, participants - 1)"
                                    style="width:36px;height:36px;border-radius:10px;border:1.5px solid var(--border);background:var(--ivory-alt);cursor:pointer;font-size:16px;color:var(--text-mid);display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                                    onmouseenter="this.style.borderColor='var(--copper)'" onmouseleave="this.style.borderColor='var(--border)'">−</button>
                            <input type="number" name="participants" x-model="participants"
                                   min="1" max="{{ $offer->max_participants ?? 10 }}" required
                                   style="flex:1;padding:10px;border-radius:10px;border:1.5px solid var(--border);background:var(--ivory-alt);font-family:'DM Sans',sans-serif;font-size:15px;font-weight:700;color:var(--text-primary);text-align:center;outline:none;">
                            <button type="button" @click="participants = Math.min({{ $offer->max_participants ?? 10 }}, participants + 1)"
                                    style="width:36px;height:36px;border-radius:10px;border:1.5px solid var(--border);background:var(--ivory-alt);cursor:pointer;font-size:16px;color:var(--text-mid);display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                                    onmouseenter="this.style.borderColor='var(--copper)'" onmouseleave="this.style.borderColor='var(--border)'">+</button>
                        </div>
                        <p style="font-size:11px;color:var(--text-soft);margin-top:5px;">Max {{ $offer->max_participants ?? 10 }} participants</p>
                    </div>

                    {{-- Total --}}
                    <div style="background:rgba(184,117,26,.06);border:1px solid rgba(184,117,26,.12);border-radius:12px;padding:16px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text-mid);margin-bottom:8px;">
                            <span>{{ number_format($offer->base_price ?? 0, 0, ',', ' ') }} FCFA × <span x-text="participants"></span></span>
                            <span x-text="formatPrice({{ $offer->base_price ?? 0 }} * participants)"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-top:10px;border-top:1px solid rgba(184,117,26,.15);">
                            <span style="font-weight:700;color:var(--text-primary);">Total</span>
                            <span style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--copper);" x-text="formatPrice({{ $offer->base_price ?? 0 }} * participants)"></span>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <button type="submit" style="width:100%;padding:14px;border-radius:100px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--copper-light),var(--copper));color:var(--ivory-alt);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:800;box-shadow:0 6px 24px rgba(184,117,26,.35);transition:all 0.25s;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 32px rgba(184,117,26,.5)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 6px 24px rgba(184,117,26,.35)'">
                        Réserver maintenant
                    </button>
                </form>

                {{-- Garanties --}}
                <div style="margin-top:20px;display:flex;flex-direction:column;gap:8px;padding-top:16px;border-top:1px solid var(--border-light);">
                    @foreach(['fa-bolt'=>'Confirmation instantanée','fa-shield-halved'=>'Paiement sécurisé','fa-rotate-left'=>'Annulation flexible'] as $icon => $text)
                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--text-mid);">
                        <i class="fas {{ $icon }}" style="font-size:11px;color:var(--copper);width:14px;"></i>
                        {{ $text }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media(max-width:1024px) {
    [style*="grid-template-columns:1fr 380px"] { grid-template-columns:1fr!important; }
    [style*="position:sticky;top:100px"] { position:static!important; }
    [style*="padding:48px"] { padding:24px!important; }
}
@media(max-width:640px) {
    [style*="padding:40px 48px"] { padding:24px!important; }
    [style*="height:480px"] { height:300px!important; }
}
</style>

@push('scripts')
<script>
function bookingForm() {
    return {
        date: '', participants: 1,
        formatPrice(p) { return new Intl.NumberFormat('fr-FR').format(Math.round(p)) + ' FCFA'; }
    }
}

// Wishlist toggle
const wishlistBtn = document.getElementById('wishlist-btn');
if (wishlistBtn) {
    wishlistBtn.addEventListener('click', async () => {
        let wishlisted = wishlistBtn.dataset.wishlisted === '1';
        const icon     = document.getElementById('wishlist-icon');

        wishlisted = !wishlisted;
        wishlistBtn.dataset.wishlisted = wishlisted ? '1' : '0';
        icon.className = (wishlisted ? 'fas' : 'far') + ' fa-heart';
        wishlistBtn.style.background = wishlisted ? 'rgba(193,68,14,.9)' : 'rgba(26,18,8,.65)';
        wishlistBtn.title = wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris';
        wishlistBtn.style.transform = 'scale(1.3)';
        setTimeout(() => wishlistBtn.style.transform = '', 200);

        try {
            const res = await fetch(wishlistBtn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
            });
            if (!res.ok) throw new Error();
        } catch(e) {
            wishlisted = !wishlisted;
            wishlistBtn.dataset.wishlisted = wishlisted ? '1' : '0';
            icon.className = (wishlisted ? 'fas' : 'far') + ' fa-heart';
            wishlistBtn.style.background = wishlisted ? 'rgba(193,68,14,.9)' : 'rgba(26,18,8,.65)';
        }
    });
}
</script>
@endpush

@endsection