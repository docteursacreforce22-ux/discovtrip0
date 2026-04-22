@extends('layouts.app')
@section('title', 'Sécurité · DiscovTrip')

@push('styles')
    @vite(['resources/css/pages/account/layout.css', 'resources/css/pages/account/security.css'])
@endpush

@section('content')
<div class="acl-root">
    @include('account._sidebar')

    <main class="acl-main" id="acl-main">

        <div class="acl-page-header">
            <div>
                <h1 class="acl-page-title">Sécurité</h1>
                <p class="acl-page-sub">Protégez votre compte</p>
            </div>
        </div>

        @if(session('success'))
        <div class="acl-alert acl-alert--ok" role="alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="acl-alert acl-alert--err" role="alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            <div>
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="sec-grid">

            {{-- Changer mot de passe --}}
            <div class="acl-card">
                <h2 class="acl-card-title">Changer le mot de passe</h2>
                <form action="{{ route('account.password.update') }}" method="POST" class="sec-form" novalidate>
                    @csrf @method('PUT')

                    <div class="sec-field @error('current_password') sec-field--err @enderror">
                        <label class="sec-label" for="current_password">Mot de passe actuel</label>
                        <div class="sec-input-wrap">
                            <input class="sec-input" type="password" id="current_password"
                                   name="current_password"
                                   placeholder="••••••••"
                                   autocomplete="current-password"
                                   @error('current_password') aria-invalid="true" aria-describedby="err-cp" @enderror>
                            <button type="button" class="sec-eye" aria-label="Afficher/masquer le mot de passe" tabindex="-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('current_password')
                            <span class="sec-err" id="err-cp" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sec-field @error('password') sec-field--err @enderror">
                        <label class="sec-label" for="password">Nouveau mot de passe</label>
                        <div class="sec-input-wrap">
                            <input class="sec-input" type="password" id="password"
                                   name="password"
                                   placeholder="8+ caractères"
                                   autocomplete="new-password"
                                   minlength="8"
                                   oninput="secUpdateStrength(this.value)"
                                   @error('password') aria-invalid="true" aria-describedby="err-np" @enderror>
                            <button type="button" class="sec-eye" aria-label="Afficher/masquer" tabindex="-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="sec-strength" id="sec-strength" aria-live="polite">
                            <div class="sec-strength-bar">
                                <div class="sec-strength-fill" id="sec-fill"></div>
                            </div>
                            <span class="sec-strength-lbl" id="sec-lbl"></span>
                        </div>
                        @error('password')
                            <span class="sec-err" id="err-np" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sec-field">
                        <label class="sec-label" for="password_confirmation">Confirmation</label>
                        <div class="sec-input-wrap">
                            <input class="sec-input" type="password" id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Répéter"
                                   autocomplete="new-password">
                            <button type="button" class="sec-eye" aria-label="Afficher/masquer" tabindex="-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="acl-btn acl-btn--primary sec-submit">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Mettre à jour le mot de passe
                    </button>
                </form>
            </div>

            {{-- Infos compte --}}
            <div class="sec-side">

                <div class="acl-card sec-info-card">
                    <h2 class="acl-card-title">Informations du compte</h2>
                    <div class="sec-info-row">
                        <div class="sec-info-lbl">Email</div>
                        <div class="sec-info-val">{{ $user->email }}</div>
                    </div>
                    <div class="sec-info-row">
                        <div class="sec-info-lbl">Vérification email</div>
                        <div>
                            @if($user->email_verified ?? $user->email_verified_at)
                                <span class="sec-badge sec-badge--ok">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                    Vérifié
                                </span>
                            @else
                                <span class="sec-badge sec-badge--warn">Non vérifié</span>
                            @endif
                        </div>
                    </div>
                    <div class="sec-info-row">
                        <div class="sec-info-lbl">Membre depuis</div>
                        <div class="sec-info-val">
                            {{ $user->created_at->locale('fr')->isoFormat('D MMMM YYYY') }}
                        </div>
                    </div>
                </div>

                <div class="acl-card sec-tips-card">
                    <h2 class="acl-card-title">Conseils de sécurité</h2>
                    <ul class="sec-tips">
                        <li>Utilisez un mot de passe unique pour DiscovTrip</li>
                        <li>Minimum 8 caractères, avec majuscules et chiffres</li>
                        <li>Ne partagez jamais vos identifiants</li>
                        <li>Déconnectez-vous sur les appareils partagés</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="acl-btn acl-btn--outline sec-logout-btn">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Se déconnecter
                    </button>
                </form>

                <div class="sec-delete-zone">
                    <div class="sec-delete-head">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                        Zone dangereuse
                    </div>
                    <p class="sec-delete-text">
                        La suppression de votre compte est <strong>irréversible</strong>.
                        Toutes vos réservations actives seront annulées et vos données effacées définitivement.
                    </p>
                    @error('delete_password')
                    <div class="acl-alert acl-alert--err" style="margin-bottom:12px" role="alert">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                        {{ $message }}
                    </div>
                    @enderror
                    <button type="button" class="sec-delete-btn" onclick="openDeleteModal()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                        Supprimer mon compte
                    </button>
                </div>

            </div>
        </div>
    </main>
</div>

{{-- Modal suppression compte --}}
<div class="acl-modal-bg" id="delete-modal-bg" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
    <div class="acl-modal">
        <button class="acl-modal-x" onclick="closeDeleteModal()" aria-label="Fermer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="acl-modal-icon" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
        </div>
        <h3 class="acl-modal-title" id="delete-modal-title">Supprimer mon compte</h3>
        <p class="acl-modal-offer" style="margin-bottom:4px">Cette action est irréversible.</p>
        <p class="acl-modal-date">Toutes vos données seront effacées définitivement.</p>
        <div class="acl-modal-policy">
            <div class="acl-modal-policy-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                Ce qui sera supprimé
            </div>
            <ul class="acl-modal-policy-list">
                <li>Votre profil et informations personnelles</li>
                <li>Vos réservations actives (annulées automatiquement)</li>
                <li>Votre liste de favoris</li>
                <li>Votre historique complet</li>
            </ul>
        </div>
        <form action="{{ route('account.delete') }}" method="POST" id="delete-account-form">
            @csrf @method('DELETE')
            <div class="sec-field" style="margin-bottom:20px">
                <label class="sec-label" for="delete_password">
                    Confirmez avec votre mot de passe
                </label>
                <div class="sec-input-wrap">
                    <input class="sec-input" type="password" id="delete_password"
                           name="password"
                           placeholder="Votre mot de passe actuel"
                           autocomplete="current-password"
                           required
                           aria-required="true">
                    <button type="button" class="sec-eye" aria-label="Afficher/masquer" tabindex="-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <div class="acl-modal-btns">
                <button type="button" class="acl-modal-btn acl-modal-btn--keep" onclick="closeDeleteModal()">
                    Annuler
                </button>
                <button type="submit" class="acl-modal-btn acl-modal-btn--confirm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                    Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Afficher/masquer mot de passe
document.querySelectorAll('.sec-eye').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = this.closest('.sec-input-wrap').querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.style.opacity = input.type === 'text' ? '1' : '.45';
        this.setAttribute('aria-label', input.type === 'text' ? 'Masquer' : 'Afficher');
    });
});

// Indicateur de force du mot de passe
function secUpdateStrength(v) {
    var fill = document.getElementById('sec-fill');
    var lbl  = document.getElementById('sec-lbl');
    if (!fill || !lbl) return;
    if (!v) { fill.style.width = '0'; lbl.textContent = ''; return; }
    var score = 0;
    if (v.length >= 8)          score++;
    if (v.length >= 12)         score++;
    if (/[A-Z]/.test(v))        score++;
    if (/[0-9]/.test(v))        score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    var levels = [
        { w:'20%',  c:'var(--b-500)', t:'Très faible' },
        { w:'40%',  c:'var(--b-300)', t:'Faible'      },
        { w:'60%',  c:'#f59e0b',      t:'Moyen'       },
        { w:'80%',  c:'var(--f-300)', t:'Fort'        },
        { w:'100%', c:'var(--f-500)', t:'Très fort'   },
    ];
    var l = levels[Math.min(score, 4)];
    fill.style.width      = l.w;
    fill.style.background = l.c;
    lbl.textContent       = l.t;
    lbl.style.color       = l.c;
}

// Modal suppression
function openDeleteModal() {
    document.getElementById('delete-modal-bg').classList.add('acl-modal-bg--open');
    document.body.style.overflow = 'hidden';
    setTimeout(function () {
        var input = document.getElementById('delete_password');
        if (input) input.focus();
    }, 300);
}
function closeDeleteModal() {
    document.getElementById('delete-modal-bg').classList.remove('acl-modal-bg--open');
    document.body.style.overflow = '';
    var input = document.getElementById('delete_password');
    if (input) input.value = '';
}
document.getElementById('delete-modal-bg').addEventListener('click', function (e) {
    if (e.target.id === 'delete-modal-bg') closeDeleteModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endpush