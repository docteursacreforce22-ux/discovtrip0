@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/bookings/create.css')
@endpush

@section('title', 'Réserver — ' . $offer->title . ' — DiscovTrip')

@section('content')

@php
    $tiers     = $offer->activeTiers;
    $firstTier = $tiers->first();
    $initPrice = $firstTier ? (float) $firstTier->price : (float) $offer->effective_price;
    $minPax    = (int) ($offer->min_participants ?? 1);
    $maxPax    = (int) ($offer->max_participants ?? 20);
@endphp

<div class="bk-page">
    <div style="max-width:960px;margin:0 auto;padding:0 1.5rem;">
        <nav class="bk-breadcrumb">
            <a href="{{ route('home') }}">Accueil</a> ›
            <a href="{{ route('offers.index') }}">Expériences</a> ›
            <a href="{{ route('offers.show', $offer->slug) }}">{{ $offer->title }}</a> ›
            <span>Réserver</span>
        </nav>
    </div>

    <div class="bk-inner">

        {{-- ── FORMULAIRE ──────────────────────────────────── --}}
        <div>
            <div class="bk-form-card">

                <div class="bk-form-header">
                    <h1>Réserver votre expérience</h1>
                    <p>Complétez les informations ci-dessous — confirmation sous 24h</p>
                </div>

                <div class="bk-form-body">
                    <form action="{{ route('bookings.store') }}" method="POST" id="bk-form" novalidate>
                        @csrf
                        <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                        <input type="hidden" name="offer_tier_id" id="bk-tier-hidden"
                               value="{{ $selectedTierId ?? ($firstTier?->id ?? '') }}">

                        {{-- Erreurs globales --}}
                        @if($errors->any())
                            <div class="bk-errors">
                                <p class="bk-errors__title">Veuillez corriger les erreurs suivantes :</p>
                                <ul class="bk-errors__list">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ① Identité --}}
                        @guest
                            <div class="bk-section">
                                <div class="bk-section-title">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Vos coordonnées
                                </div>
                                <div class="bk-grid-2" style="margin-bottom:1rem;">
                                    <div class="bk-field">
                                        <label>Prénom <span class="bk-req">*</span></label>
                                        <input type="text" name="guest_first_name"
                                               class="bk-input {{ $errors->has('guest_first_name') ? '--error' : '' }}"
                                               value="{{ old('guest_first_name') }}"
                                               placeholder="Jean" autocomplete="given-name" required>
                                        @error('guest_first_name')
                                            <span class="bk-error-msg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="bk-field">
                                        <label>Nom <span class="bk-req">*</span></label>
                                        <input type="text" name="guest_last_name"
                                               class="bk-input {{ $errors->has('guest_last_name') ? '--error' : '' }}"
                                               value="{{ old('guest_last_name') }}"
                                               placeholder="Dupont" autocomplete="family-name" required>
                                        @error('guest_last_name')
                                            <span class="bk-error-msg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="bk-grid-2">
                                    <div class="bk-field">
                                        <label>Email <span class="bk-req">*</span></label>
                                        <input type="email" name="guest_email"
                                               class="bk-input {{ $errors->has('guest_email') ? '--error' : '' }}"
                                               value="{{ old('guest_email') }}"
                                               placeholder="jean@example.com" autocomplete="email" required>
                                        <span class="bk-field-hint">Pour recevoir votre confirmation</span>
                                        @error('guest_email')
                                            <span class="bk-error-msg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="bk-field">
                                        <label>Téléphone / WhatsApp</label>
                                        <input type="tel" name="guest_phone"
                                               class="bk-input"
                                               value="{{ old('guest_phone') }}"
                                               placeholder="+229 01 00 00 00 00" autocomplete="tel">
                                    </div>
                                </div>
                                <div class="bk-guest-hint">
                                    Vous avez déjà un compte ?
                                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">
                                        Connectez-vous
                                    </a>
                                    pour accéder à votre historique de réservations.
                                </div>
                            </div>
                        @endguest

                        @auth
                            <div class="bk-section">
                                <div class="bk-section-title">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Réservation au nom de
                                </div>
                                <div class="bk-auth-box">
                                    <div class="bk-auth-avatar">
                                        {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="bk-auth-name">
                                            {{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? auth()->user()->name ?? '' }}
                                        </div>
                                        <div class="bk-auth-email">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                            </div>
                        @endauth

                        {{-- ② Niveau --}}
                        @if($tiers->isNotEmpty())
                            <div class="bk-section">
                                <div class="bk-section-title">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    Niveau d'expérience
                                </div>
                                <div class="bk-tier-list">
                                    @foreach($tiers as $tier)
                                        @php
                                            $isSelected = (old('offer_tier_id', $selectedTierId ?? $firstTier?->id) == $tier->id);
                                        @endphp
                                        <label
                                            class="bk-tier-opt {{ $isSelected ? '--selected' : '' }}"
                                            onclick="bkSelectTier({{ $tier->id }}, {{ $tier->price }}, '{{ addslashes($tier->label) }}')"
                                        >
                                            <input type="radio" name="_tier_visual" value="{{ $tier->id }}"
                                                   {{ $isSelected ? 'checked' : '' }}>
                                            <div class="bk-tier-radio"></div>
                                            <span class="bk-tier-emoji">{{ $tier->emoji }}</span>
                                            <div class="bk-tier-info">
                                                <div class="bk-tier-name">{{ $tier->label }}</div>
                                                @if($tier->tagline)
                                                    <div class="bk-tier-tagline">{{ $tier->tagline }}</div>
                                                @endif
                                            </div>
                                            <div class="bk-tier-price">
                                                {{ $tier->price_is_indicative ? 'dès ' : '' }}{{ number_format($tier->price, 0, ',', ' ') }} FCFA
                                                <span>{{ $tier->price_eur }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- ③ Date & heure --}}
                        <div class="bk-section">
                            <div class="bk-section-title">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Date & heure
                            </div>
                            <div class="bk-grid-2">
                                <div class="bk-field">
                                    <label>Date de l'expérience <span class="bk-req">*</span></label>
                                    <input type="date" name="date"
                                           class="bk-input {{ $errors->has('date') ? '--error' : '' }}"
                                           value="{{ old('date') }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}"
                                           required>
                                    @error('date')
                                        <span class="bk-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="bk-field">
                                    <label>Heure souhaitée</label>
                                    <select name="time" class="bk-select">
                                        <option value="">Flexible</option>
                                        <option value="07:00" {{ old('time') === '07:00' ? 'selected' : '' }}>07:00 — Tôt le matin</option>
                                        <option value="08:00" {{ old('time') === '08:00' ? 'selected' : '' }}>08:00 — Matin</option>
                                        <option value="09:00" {{ old('time') === '09:00' ? 'selected' : '' }}>09:00 — Matin</option>
                                        <option value="10:00" {{ old('time') === '10:00' ? 'selected' : '' }}>10:00 — Matin</option>
                                        <option value="14:00" {{ old('time') === '14:00' ? 'selected' : '' }}>14:00 — Après-midi</option>
                                        <option value="15:00" {{ old('time') === '15:00' ? 'selected' : '' }}>15:00 — Après-midi</option>
                                        <option value="16:00" {{ old('time') === '16:00' ? 'selected' : '' }}>16:00 — Fin d'après-midi</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ④ Participants --}}
                        <div class="bk-section">
                            <div class="bk-section-title">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Participants
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                                <div>
                                    <div class="bk-stepper">
                                        <button type="button" class="bk-stepper__btn" id="bk-btn-minus" onclick="bkChangePax(-1)">−</button>
                                        <div class="bk-stepper__val" id="bk-pax-display">{{ old('participants', $minPax) }}</div>
                                        <button type="button" class="bk-stepper__btn" id="bk-btn-plus"  onclick="bkChangePax(1)">+</button>
                                    </div>
                                    <input type="hidden" name="participants" id="bk-pax-input" value="{{ old('participants', $minPax) }}">
                                    @error('participants')
                                        <span class="bk-error-msg" style="margin-top:.35rem;display:block;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <span class="bk-field-hint">Minimum {{ $minPax }} · Maximum {{ $maxPax }} pers.</span>
                            </div>
                        </div>

                        {{-- ⑤ Message --}}
                        <div class="bk-section">
                            <div class="bk-section-title">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                Message (optionnel)
                            </div>
                            <div class="bk-field">
                                <label>Précisions ou demandes particulières</label>
                                <textarea name="message" class="bk-textarea"
                                          placeholder="Allergies, mobilité réduite, anniversaire, groupe scolaire…"
                                          rows="3">{{ old('message') }}</textarea>
                            </div>
                        </div>

                        {{-- ⑥ Mode de paiement --}}
                        @if($hasOnline && $hasOnSite)
                            <div class="bk-section">
                                <div class="bk-section-title">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    Mode de paiement
                                </div>
                                <div class="bk-payment-opts">
                                    <label class="bk-payment-opt --selected" onclick="bkSelectPayment(this)">
                                        <input type="radio" name="payment_choice" value="on_site" checked>
                                        <div class="bk-payment-radio"></div>
                                        <span class="bk-payment-icon">📅</span>
                                        <div>
                                            <div class="bk-payment-label">Payer sur place</div>
                                            <div class="bk-payment-hint">Le jour de l'expérience, en espèces ou mobile money</div>
                                        </div>
                                    </label>
                                    <label class="bk-payment-opt" onclick="bkSelectPayment(this)">
                                        <input type="radio" name="payment_choice" value="online">
                                        <div class="bk-payment-radio"></div>
                                        <span class="bk-payment-icon">💳</span>
                                        <div>
                                            <div class="bk-payment-label">Payer en ligne maintenant</div>
                                            <div class="bk-payment-hint">Carte bancaire ou mobile money — confirmation immédiate</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @elseif($hasOnline)
                            <input type="hidden" name="payment_choice" value="online">
                        @else
                            <input type="hidden" name="payment_choice" value="on_site">
                        @endif

                        {{-- Submit --}}
                        <button type="submit" class="bk-submit" id="bk-submit-btn">
                            Confirmer ma réservation
                        </button>
                        <p class="bk-secure-note">
                            🔒 Données sécurisées · Annulation gratuite 48h avant l'expérience
                        </p>

                    </form>
                </div>{{-- fin form-body --}}
            </div>{{-- fin form-card --}}
        </div>

        {{-- ── SIDEBAR RÉCAPITULATIF ───────────────────────── --}}
        <aside>
            <div class="bk-recap-card">
                @if($offer->cover_image)
                    <img src="{{ Storage::url($offer->cover_image) }}"
                         alt="{{ $offer->title }}" class="bk-recap-img">
                @else
                    <div class="bk-recap-img-ph">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--f-500)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                    </div>
                @endif

                <div class="bk-recap-header">
                    <div class="bk-recap-header__title">Votre expérience</div>
                    <div class="bk-recap-offer-name">{{ $offer->title }}</div>
                    <div class="bk-recap-city">
                        📍 {{ $offer->city->name }} · ⏱ {{ $offer->duration_formatted }}
                    </div>
                </div>

                <div class="bk-recap-body">
                    <div class="bk-recap-row">
                        <span>Niveau</span>
                        <span id="bk-recap-tier">{{ $firstTier ? $firstTier->label : 'Standard' }}</span>
                    </div>
                    <div class="bk-recap-row">
                        <span>Prix unitaire</span>
                        <span id="bk-recap-unit">{{ number_format($initPrice, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="bk-recap-row">
                        <span>Participants</span>
                        <span id="bk-recap-pax">× {{ $minPax }}</span>
                    </div>
                    @if($offer->is_promo)
                        <div class="bk-recap-row" style="color:var(--b-500);">
                            <span>Réduction promo</span>
                            <span>−{{ $offer->promo_discount }}%</span>
                        </div>
                    @endif
                    <div class="bk-recap-total">
                        <span>Total estimé</span>
                        <span id="bk-recap-total">{{ number_format($initPrice * $minPax, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                <div class="bk-recap-footer">
                    <div class="bk-recap-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Paiement 100% sécurisé
                    </div>
                    <div class="bk-recap-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        Confirmation sous 24h
                    </div>
                    <div class="bk-recap-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/></svg>
                        Annulation gratuite jusqu'à 48h avant
                    </div>
                </div>
            </div>
        </aside>

    </div>{{-- fin bk-inner --}}
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var _minPax    = {{ $minPax }};
    var _maxPax    = {{ $maxPax }};
    var _pax       = {{ old('participants', $minPax) }};
    var _tierPrice = {{ $initPrice }};
    var _tierLabel = '{{ addslashes($firstTier ? $firstTier->label : 'Standard') }}';

    window.bkSelectTier = function (tierId, price, label) {
        var hid = document.getElementById('bk-tier-hidden');
        if (hid) hid.value = tierId;
        document.querySelectorAll('.bk-tier-opt').forEach(function (el) {
            el.classList.remove('--selected');
        });
        var radio = document.querySelector('.bk-tier-opt input[value="' + tierId + '"]');
        if (radio) radio.closest('.bk-tier-opt').classList.add('--selected');
        _tierPrice = parseFloat(price) || _tierPrice;
        _tierLabel = label || _tierLabel;
        bkUpdateRecap();
    };

    window.bkChangePax = function (delta) {
        var next = _pax + delta;
        if (next < _minPax || next > _maxPax) return;
        _pax = next;
        var display = document.getElementById('bk-pax-display');
        var hidden  = document.getElementById('bk-pax-input');
        var minus   = document.getElementById('bk-btn-minus');
        var plus    = document.getElementById('bk-btn-plus');
        if (display) display.textContent = _pax;
        if (hidden)  hidden.value = _pax;
        if (minus)   minus.disabled = (_pax <= _minPax);
        if (plus)    plus.disabled  = (_pax >= _maxPax);
        bkUpdateRecap();
    };

    function bkUpdateRecap() {
        var fmt     = function (n) { return n.toLocaleString('fr-FR') + ' FCFA'; };
        var tierEl  = document.getElementById('bk-recap-tier');
        var unitEl  = document.getElementById('bk-recap-unit');
        var paxEl   = document.getElementById('bk-recap-pax');
        var totalEl = document.getElementById('bk-recap-total');
        if (tierEl)  tierEl.textContent  = _tierLabel;
        if (unitEl)  unitEl.textContent  = fmt(_tierPrice);
        if (paxEl)   paxEl.textContent   = '× ' + _pax;
        if (totalEl) totalEl.textContent = fmt(_tierPrice * _pax);
    }

    window.bkSelectPayment = function (labelEl) {
        document.querySelectorAll('.bk-payment-opt').forEach(function (el) {
            el.classList.remove('--selected');
        });
        labelEl.classList.add('--selected');
        var radio = labelEl.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    };

    document.addEventListener('DOMContentLoaded', function () {
        var minus = document.getElementById('bk-btn-minus');
        var plus  = document.getElementById('bk-btn-plus');
        if (minus) minus.disabled = (_pax <= _minPax);
        if (plus)  plus.disabled  = (_pax >= _maxPax);
        bkUpdateRecap();
    });

}());
</script>
@endpush