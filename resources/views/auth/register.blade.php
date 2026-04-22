@extends('layouts.app')
@section('title', 'Créer un compte · DiscovTrip')

@push('styles')
    @vite('resources/css/pages/auth/auth.css')
@endpush

@section('content')
<div class="auth-page">

    <div class="auth-bg">
        <div class="auth-bg-grid"></div>
        <div class="auth-bg-glow auth-bg-glow--bottom"></div>
    </div>

    <div class="auth-wrap auth-wrap--wide">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="auth-logo">
            <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                <circle cx="20" cy="20" r="19" stroke="#E8BC3A" stroke-width="1.5"/>
                <path d="M12 28 L20 10 L28 28" stroke="#E8BC3A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.5 22 L25.5 22" stroke="#E8BC3A" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span class="auth-logo-text">DiscovTrip</span>
        </a>

        <div class="auth-card">

            <div class="auth-card-head">
                <h1 class="auth-title">Créer un compte</h1>
                <p class="auth-subtitle">
                    Déjà inscrit ?
                    <a href="{{ route('login') }}" class="auth-link">Se connecter</a>
                </p>
            </div>

            @if($errors->any())
            <div class="auth-alert auth-alert--error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" class="auth-form">
                @csrf

                {{-- Prénom + Nom --}}
                <div class="auth-row">
                    <div class="auth-field @error('first_name') auth-field--error @enderror">
                        <label class="auth-label" for="first_name">
                            Prénom <span class="auth-required">*</span>
                        </label>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input class="auth-input" type="text" id="first_name" name="first_name"
                                   value="{{ old('first_name') }}" placeholder="Koffi"
                                   autocomplete="given-name" required>
                        </div>
                        @error('first_name')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="auth-field @error('last_name') auth-field--error @enderror">
                        <label class="auth-label" for="last_name">
                            Nom <span class="auth-required">*</span>
                        </label>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input class="auth-input" type="text" id="last_name" name="last_name"
                                   value="{{ old('last_name') }}" placeholder="Mensah"
                                   autocomplete="family-name" required>
                        </div>
                        @error('last_name')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="auth-field @error('email') auth-field--error @enderror">
                    <label class="auth-label" for="email">
                        Email <span class="auth-required">*</span>
                    </label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input class="auth-input" type="email" id="email" name="email"
                               value="{{ old('email') }}" placeholder="vous@email.com"
                               autocomplete="email" required>
                    </div>
                    @error('email')<span class="auth-error">{{ $message }}</span>@enderror
                </div>

                {{-- Téléphone --}}
                <div class="auth-field @error('phone') auth-field--error @enderror">
                    <label class="auth-label" for="phone">
                        Téléphone
                        <span class="auth-optional">— facultatif · format +229 01 XX XX XX XX</span>
                    </label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.27 2 2 0 0 1 3.63 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 5.49 5.49l.96-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <input class="auth-input" type="tel" id="phone" name="phone"
                               value="{{ old('phone') }}" placeholder="+229 01 00 00 00 00"
                               autocomplete="tel">
                    </div>
                    @error('phone')<span class="auth-error">{{ $message }}</span>@enderror
                </div>

                {{-- Mot de passe --}}
                <div class="auth-row">
                    <div class="auth-field @error('password') auth-field--error @enderror">
                        <label class="auth-label" for="password">
                            Mot de passe <span class="auth-required">*</span>
                        </label>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input class="auth-input" type="password" id="password" name="password"
                                   placeholder="8+ caractères" autocomplete="new-password" required minlength="8">
                            <button type="button" class="auth-toggle-pw" tabindex="-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password_confirmation">
                            Confirmation <span class="auth-required">*</span>
                        </label>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input class="auth-input" type="password" id="password_confirmation"
                                   name="password_confirmation" placeholder="Répéter"
                                   autocomplete="new-password" required>
                            <button type="button" class="auth-toggle-pw" tabindex="-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Consentement --}}
                <div class="auth-consent @error('consent') auth-field--error @enderror">
                    <input type="checkbox" id="consent" name="consent"
                           class="auth-checkbox" {{ old('consent') ? 'checked' : '' }}>
                    <label for="consent" class="auth-consent-label">
                        J'accepte les
                        <a href="{{ route('cgu') }}" class="auth-link" target="_blank">conditions d'utilisation</a>
                        et la
                        <a href="{{ route('privacy') }}" class="auth-link" target="_blank">politique de confidentialité</a>.
                        <span class="auth-required">*</span>
                    </label>
                </div>
                @error('consent')<span class="auth-error">{{ $message }}</span>@enderror

                <button type="submit" class="auth-btn">
                    Créer mon compte
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>

            </form>
        </div>

        <a href="{{ route('home') }}" class="auth-back">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Retour à l'accueil
        </a>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.auth-toggle-pw').forEach(btn => {
    btn.addEventListener('click', function () {
        const input = this.closest('.auth-input-wrap').querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.style.opacity = input.type === 'text' ? '1' : '0.45';
    });
});
</script>
@endpush