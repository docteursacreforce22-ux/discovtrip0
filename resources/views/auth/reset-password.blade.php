@extends('layouts.app')
@section('title', 'Nouveau mot de passe · DiscovTrip')

@push('styles')
    @vite('resources/css/pages/auth/auth.css')
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-bg">
        <div class="auth-bg-grid"></div>
        <div class="auth-bg-glow auth-bg-glow--bottom"></div>
    </div>

    <div class="auth-wrap">

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
                <div class="auth-icon-badge">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--a-500)" stroke-width="1.8"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h1 class="auth-title">Nouveau mot de passe</h1>
                <p class="auth-subtitle">Choisissez un mot de passe sécurisé d'au moins 8 caractères.</p>
            </div>

            @if($errors->any())
            <div class="auth-alert auth-alert--error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form action="{{ route('password.reset.update') }}" method="POST" class="auth-form">
                @csrf

                {{-- Token + Email (cachés) --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- Email affiché en lecture seule --}}
                <div class="auth-field">
                    <label class="auth-label">Email</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input class="auth-input" type="email" value="{{ $email }}"
                               disabled style="background:var(--cream-3);color:var(--tx-muted);">
                    </div>
                </div>

                {{-- Nouveau mot de passe --}}
                <div class="auth-field @error('password') auth-field--error @enderror">
                    <label class="auth-label" for="password">
                        Nouveau mot de passe <span class="auth-required">*</span>
                    </label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input class="auth-input" type="password" id="password" name="password"
                               placeholder="8+ caractères"
                               autocomplete="new-password" required minlength="8"
                               oninput="checkStrength(this.value)">
                        <button type="button" class="auth-toggle-pw" tabindex="-1">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    {{-- Barre de force --}}
                    <div class="auth-strength">
                        <div class="auth-strength-bar" id="strength-bar"></div>
                    </div>
                    <span class="auth-strength-label" id="strength-label"></span>
                    @error('password')<span class="auth-error">{{ $message }}</span>@enderror
                </div>

                {{-- Confirmation --}}
                <div class="auth-field">
                    <label class="auth-label" for="password_confirmation">
                        Confirmer le mot de passe <span class="auth-required">*</span>
                    </label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input class="auth-input" type="password" id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Répéter le mot de passe"
                               autocomplete="new-password" required>
                        <button type="button" class="auth-toggle-pw" tabindex="-1">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn">
                    Réinitialiser le mot de passe
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>

            </form>

        </div>

        <a href="{{ route('login') }}" class="auth-back">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Retour à la connexion
        </a>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle visibilité mot de passe
document.querySelectorAll('.auth-toggle-pw').forEach(btn => {
    btn.addEventListener('click', function () {
        const input = this.closest('.auth-input-wrap').querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.style.opacity = input.type === 'text' ? '1' : '0.45';
    });
});

// Indicateur de force
function checkStrength(val) {
    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    if (!bar) return;

    let score = 0;
    if (val.length >= 8)              score++;
    if (val.length >= 12)             score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const levels = [
        { pct: '20%', color: 'var(--b-500)',  text: 'Très faible' },
        { pct: '40%', color: 'var(--b-300)',  text: 'Faible'      },
        { pct: '60%', color: 'var(--a-400)',  text: 'Moyen'       },
        { pct: '80%', color: 'var(--f-500)',  text: 'Fort'        },
        { pct: '100%',color: 'var(--f-700)',  text: 'Très fort'   },
    ];

    const lvl       = levels[Math.min(score, 4)];
    bar.style.width = val.length ? lvl.pct : '0';
    bar.style.background = lvl.color;
    label.textContent    = val.length ? lvl.text : '';
    label.style.color    = lvl.color;
}
</script>
@endpush