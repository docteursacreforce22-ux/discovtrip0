<x-emails.layout
    title="[DiscovTrip] Nouveau message — {{ $subjects[$data['subject']] }}"
    preheader="Nouveau message de {{ $data['first_name'] }} {{ $data['last_name'] }} · {{ $subjects[$data['subject']] }}">

    {{-- Hero --}}
    <div class="email-hero" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d1b4e 100%);">
        <div style="font-size:44px; margin-bottom:16px;">📬</div>
        <h1 class="email-hero-title">
            Nouveau<br><em>message reçu</em>
        </h1>
        <p class="email-hero-sub">
            {{ $subjects[$data['subject']] }} · {{ now()->locale('fr')->isoFormat('D MMMM YYYY [à] HH[h]mm') }}
        </p>
    </div>

    {{-- Body --}}
    <div class="email-body">

        {{-- Identité expéditeur --}}
        <div class="email-card" style="border-left-color:#7c3aed;">
            <div class="email-card-title" style="color:#7c3aed;">👤 Expéditeur</div>
            <div class="email-row">
                <span class="email-row-label">Nom</span>
                <span class="email-row-value">{{ $data['first_name'] }} {{ $data['last_name'] }}</span>
            </div>
            <div class="email-row">
                <span class="email-row-label">Email</span>
                <span class="email-row-value">
                    <a href="mailto:{{ $data['email'] }}" style="color:#D4A20F;">{{ $data['email'] }}</a>
                </span>
            </div>
            @if($data['phone'] ?? null)
            <div class="email-row">
                <span class="email-row-label">Téléphone</span>
                <span class="email-row-value">
                    <a href="tel:{{ $data['phone'] }}" style="color:#D4A20F;">{{ $data['phone'] }}</a>
                </span>
            </div>
            @endif
            <div class="email-row">
                <span class="email-row-label">Sujet</span>
                <span class="email-row-value">
                    <span style="background:rgba(212,162,15,.15);color:#8a6a00;padding:3px 10px;border-radius:50px;font-size:12px;">
                        {{ $subjects[$data['subject']] }}
                    </span>
                </span>
            </div>
        </div>

        {{-- Message --}}
        <div style="margin-top:20px;">
            <div style="font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#9a8a78;margin-bottom:10px;">
                💬 Message
            </div>
            <div style="background:#f9f5eb;border:1.5px solid #e8dfc8;border-radius:12px;padding:20px 22px;font-size:15px;line-height:1.75;color:#1F3A2A;white-space:pre-wrap;">{{ $data['message'] }}</div>
        </div>

        <div class="email-cta-wrap" style="margin-top:28px;">
            <a href="mailto:{{ $data['email'] }}?subject=Re: {{ $subjects[$data['subject']] }} — DiscovTrip"
               class="email-cta">
                ↩️ Répondre à {{ $data['first_name'] }}
            </a>
        </div>

        <div class="email-divider"></div>

        <p style="font-size:12px;color:#9a8a78;text-align:center;margin:0;">
            Message reçu via le formulaire de contact DiscovTrip ·
            IP : {{ request()->ip() }}
        </p>
    </div>

</x-emails.layout>