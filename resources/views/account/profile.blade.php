@extends('layouts.app')
@section('title', 'Mon profil · DiscovTrip')

@push('styles')
    @vite(['resources/css/pages/account/layout.css', 'resources/css/pages/account/profile.css'])
@endpush

@section('content')
<div class="acl-root">
    @include('account._sidebar')

    <main class="acl-main" id="acl-main">

        <div class="acl-page-header">
            <div>
                <h1 class="acl-page-title">Mon profil</h1>
                <p class="acl-page-sub">Gérez vos informations personnelles</p>
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

        <form action="{{ route('account.profile.update') }}"
              method="POST"
              enctype="multipart/form-data"
              class="prf-grid"
              novalidate>
            @csrf @method('PUT')

            {{-- Colonne principale --}}
            <div class="prf-col-main">

                <div class="acl-card">
                    <h2 class="acl-card-title">Informations personnelles</h2>

                    <div class="prf-row">
                        <div class="prf-field @error('first_name') prf-field--err @enderror">
                            <label class="prf-label" for="first_name">
                                Prénom <span class="prf-req" aria-hidden="true">*</span>
                            </label>
                            <input class="prf-input" type="text" id="first_name" name="first_name"
                                   value="{{ old('first_name', $user->first_name) }}"
                                   autocomplete="given-name" required
                                   aria-required="true"
                                   @error('first_name') aria-invalid="true" aria-describedby="err-first_name" @enderror>
                            @error('first_name')
                                <span class="prf-err" id="err-first_name" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="prf-field @error('last_name') prf-field--err @enderror">
                            <label class="prf-label" for="last_name">
                                Nom <span class="prf-req" aria-hidden="true">*</span>
                            </label>
                            <input class="prf-input" type="text" id="last_name" name="last_name"
                                   value="{{ old('last_name', $user->last_name) }}"
                                   autocomplete="family-name" required
                                   aria-required="true"
                                   @error('last_name') aria-invalid="true" aria-describedby="err-last_name" @enderror>
                            @error('last_name')
                                <span class="prf-err" id="err-last_name" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="prf-field @error('phone') prf-field--err @enderror">
                        <label class="prf-label" for="phone">
                            Téléphone
                            <span class="prf-hint">— facultatif · +229 01 XX XX XX XX</span>
                        </label>
                        <input class="prf-input" type="tel" id="phone" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+229 01 00 00 00 00"
                               autocomplete="tel"
                               @error('phone') aria-invalid="true" aria-describedby="err-phone" @enderror>
                        @error('phone')
                            <span class="prf-err" id="err-phone" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="prf-row">
                        <div class="prf-field @error('birthday') prf-field--err @enderror">
                            <label class="prf-label" for="birthday">Date de naissance</label>
                            <input class="prf-input" type="date" id="birthday" name="birthday"
                                   value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}"
                                   @error('birthday') aria-invalid="true" @enderror>
                            @error('birthday')
                                <span class="prf-err" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="prf-field @error('gender') prf-field--err @enderror">
                            <label class="prf-label" for="gender">Genre</label>
                            <select class="prf-input prf-select" id="gender" name="gender">
                                <option value="">— Non précisé</option>
                                @foreach([
                                    'male'             => 'Homme',
                                    'female'           => 'Femme',
                                    'other'            => 'Autre',
                                    'prefer_not_to_say'=> 'Préfère ne pas dire',
                                ] as $v => $l)
                                    <option value="{{ $v }}" {{ old('gender', $user->gender) === $v ? 'selected' : '' }}>
                                        {{ $l }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="prf-row">
                        <div class="prf-field @error('nationality') prf-field--err @enderror">
                            <label class="prf-label" for="nationality">Nationalité</label>
                            <input class="prf-input" type="text" id="nationality" name="nationality"
                                   value="{{ old('nationality', $user->nationality) }}"
                                   placeholder="Béninois, Français…">
                            @error('nationality')
                                <span class="prf-err" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="prf-field @error('locale') prf-field--err @enderror">
                            <label class="prf-label" for="locale">Langue</label>
                            <select class="prf-input prf-select" id="locale" name="locale">
                                <option value="fr" {{ old('locale', $user->locale ?? 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ old('locale', $user->locale ?? 'fr') === 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>
                    </div>

                    <div class="prf-field @error('bio') prf-field--err @enderror">
                        <label class="prf-label" for="bio">
                            Bio
                            <span class="prf-hint">— max. 500 caractères</span>
                        </label>
                        <textarea class="prf-input prf-textarea"
                                  id="bio" name="bio"
                                  placeholder="Parlez-nous de vos centres d'intérêt…"
                                  maxlength="500"
                                  aria-describedby="bio-counter">{{ old('bio', $user->bio) }}</textarea>
                        <span class="prf-counter" id="bio-counter">
                            <span id="bio-count">{{ strlen($user->bio ?? '') }}</span>/500
                        </span>
                        @error('bio')
                            <span class="prf-err" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Colonne latérale --}}
            <div class="prf-col-side">

                <div class="acl-card prf-avatar-card">
                    <h2 class="acl-card-title">Photo de profil</h2>
                    <div class="prf-avatar-wrap" id="prf-avatar-wrap" aria-label="Aperçu de la photo">
                        {{-- CORRECTION : utiliser profile_picture et asset() --}}
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                 alt="{{ $user->name }}"
                                 id="prf-avatar-img"
                                 width="100" height="100">
                        @else
                            @php
                                $init = strtoupper(
                                    substr($user->first_name ?? $user->name ?? 'V', 0, 1) .
                                    substr($user->last_name ?? '', 0, 1)
                                );
                            @endphp
                            <span class="prf-avatar-init" id="prf-avatar-init" aria-hidden="true">
                                {{ $init ?: 'DT' }}
                            </span>
                        @endif
                    </div>
                    <label for="profile_picture" class="prf-upload-btn">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Choisir une photo
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture"
                           accept="image/jpeg,image/png,image/webp"
                           class="prf-file-hidden"
                           aria-label="Choisir une photo de profil">
                    <p class="prf-upload-hint">JPG, PNG, WEBP · max 2 Mo</p>
                    @error('profile_picture')
                        <span class="prf-err" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="acl-btn acl-btn--primary prf-save-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Enregistrer les modifications
                </button>

                <a href="{{ route('account.security') }}" class="prf-sec-link">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Changer mon mot de passe
                </a>

            </div>

        </form>
    </main>
</div>
@endsection

@push('scripts')
<script>
// Aperçu photo avant upload
var fileInput = document.getElementById('profile_picture');
if (fileInput) {
    fileInput.addEventListener('change', function () {
        var file = this.files[0];
        if (!file) return;
        // Vérifier la taille (max 2 Mo)
        if (file.size > 2 * 1024 * 1024) {
            alert('La photo ne doit pas dépasser 2 Mo.');
            this.value = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var wrap = document.getElementById('prf-avatar-wrap');
            wrap.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu" id="prf-avatar-img" width="100" height="100">';
        };
        reader.readAsDataURL(file);
    });
}

// Compteur bio
var bio = document.getElementById('bio');
var cnt = document.getElementById('bio-count');
if (bio && cnt) {
    bio.addEventListener('input', function () {
        cnt.textContent  = bio.value.length;
        cnt.style.color  = bio.value.length > 480 ? 'var(--b-500)' : '';
    });
}
</script>
@endpush