@extends('layouts.app')
@section('title', 'Mot de passe oublié · DiscovTrip')

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
                <h1 class="auth-title">Mot de passe oublié ?</h1>
                <p class="auth-subtitle">
                    Entrez votre email et nous vous enverrons un lien de réinitialisation.
                </p>
            </div>

            @if(session('status'))
            <div class="auth-alert auth-alert--success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="auth-alert auth-alert--error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-field @error('email') auth-field--error @enderror">
                    <label class="auth-label" for="email">Adresse email</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input class="auth-input" type="email" id="email" name="email"
                               value="{{ old('email') }}" placeholder="vous@email.com"
                               autocomplete="email" autofocus required>
                    </div>
                    @error('email')<span class="auth-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="auth-btn">
                    Envoyer le lien de réinitialisation
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