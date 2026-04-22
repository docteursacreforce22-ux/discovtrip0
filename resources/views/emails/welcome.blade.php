<x-emails.layout
    title="Bienvenue sur DiscovTrip 🌍"
    preheader="Bienvenue {{ $user->first_name }} ! Votre compte DiscovTrip est prêt. Découvrez le Bénin authentique.">

    {{-- Hero --}}
    <div class="email-hero">
        <div style="font-size:44px; margin-bottom:16px;">🌍</div>
        <h1 class="email-hero-title">
            Bienvenue,<br><em>{{ $user->first_name }}</em> !
        </h1>
        <p class="email-hero-sub">
            Votre compte DiscovTrip est prêt. L'aventure commence maintenant.
        </p>
    </div>

    {{-- Body --}}
    <div class="email-body">
        <p class="email-greeting">Bonjour {{ $user->first_name }},</p>
        <p class="email-p">
            Merci de rejoindre DiscovTrip, la plateforme d'expériences authentiques au Bénin.
            Votre compte est activé et vous pouvez dès maintenant explorer nos destinations
            et réserver vos premières expériences.
        </p>

        {{-- Ce que tu peux faire --}}
        <div class="email-card">
            <div class="email-card-title">✨ Ce qui vous attend</div>
            <div class="email-row">
                <span class="email-row-label">🏛️ Destinations</span>
                <span class="email-row-value">Cotonou, Ganvié, Ouidah, Abomey…</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">🎯 Expériences uniques</span>
                <span class="email-row-value">Guides certifiés, 100% authentiques</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">🛡️ Annulation gratuite</span>
                <span class="email-row-value">Jusqu'à 48h avant chaque expérience</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">💳 Paiement sécurisé</span>
                <span class="email-row-value">CB, Mobile Money, FCFA</span>
            </div>
        </div>

        <div class="email-cta-wrap">
            <a href="{{ route('destinations') }}" class="email-cta">
                🗺️ Explorer les destinations
            </a>
        </div>

        <div class="email-divider"></div>

        <p class="email-p" style="font-size:13px; color:#9a8a78; text-align:center;">
            Des questions ? Notre équipe vous répond sous 24h.<br>
            <a href="{{ route('contact') }}" style="color:#c49a0d; font-weight:600;">Nous contacter</a>
            &nbsp;·&nbsp;
            <a href="{{ route('faq') }}" style="color:#c49a0d; font-weight:600;">FAQ</a>
        </p>
    </div>

</x-emails.layout>