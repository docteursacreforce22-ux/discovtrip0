<x-emails.layout
    title="Votre message a bien été reçu — DiscovTrip"
    preheader="Merci {{ $name }} ! Nous avons bien reçu votre message et vous répondrons sous 24h.">

    {{-- Hero --}}
    <div class="email-hero">
        <div style="font-size:44px; margin-bottom:16px;">💌</div>
        <h1 class="email-hero-title">
            Message<br><em>bien reçu !</em>
        </h1>
        <p class="email-hero-sub">
            Merci de nous avoir contactés. Nous vous répondrons sous 24h.
        </p>
    </div>

    {{-- Body --}}
    <div class="email-body">
        <p class="email-greeting">Bonjour {{ $name }},</p>
        <p class="email-p">
            Nous avons bien reçu votre message et en prenons connaissance avec attention.
            Notre équipe locale au Bénin vous répondra <strong>sous 24h ouvrées</strong>.
        </p>

        {{-- Promesses --}}
        <div class="email-card">
            <div class="email-card-title">🤝 Nos engagements</div>
            <div class="email-row">
                <span class="email-row-label">⚡ Délai de réponse</span>
                <span class="email-row-value">Sous 24h ouvrées</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">🕐 Horaires</span>
                <span class="email-row-value">Lun–Ven 8h–18h · Sam 9h–14h</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">📍 Notre équipe</span>
                <span class="email-row-value">Basée à Cotonou, Bénin</span>
            </div>
        </div>

        <div class="email-alert email-alert--green">
            💡 <strong>Besoin d'une réponse urgente ?</strong>
            Contactez-nous directement sur
            <a href="https://wa.me/22901000000" style="color:#1F6B44;font-weight:700;">WhatsApp</a>
            ou appelez le <a href="tel:+22901000000" style="color:#1F6B44;font-weight:700;">+229 01 00 00 00 00</a>.
        </div>

        <div class="email-divider"></div>

        <p class="email-p" style="text-align:center;">
            En attendant, explorez nos expériences au Bénin :
        </p>

        <div class="email-cta-wrap">
            <a href="{{ route('destinations') }}" class="email-cta">
                🗺️ Découvrir les destinations
            </a>
        </div>

        <div class="email-divider"></div>

        <p class="email-p" style="font-size:13px;color:#9a8a78;text-align:center;">
            Si vous n'êtes pas à l'origine de ce message,
            <a href="{{ route('contact') }}" style="color:#c49a0d;font-weight:600;">contactez-nous</a> immédiatement.
        </p>
    </div>

</x-emails.layout>