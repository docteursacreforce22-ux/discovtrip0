{{--
    Layout partagé pour tous les emails DiscovTrip
    Usage : @include('emails._layout', ['slot' => $slot, 'preheader' => '...'])
    Ou directement via @extends si tu passes en Mailable composant
--}}
<!DOCTYPE html>
<html lang="fr" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="x-apple-disable-message-reformatting">
<meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
<title>{{ $title ?? 'DiscovTrip' }}</title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<![endif]-->
<style>
/* Reset */
*, *::before, *::after { box-sizing: border-box; }
body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }

/* Base */
body { margin: 0; padding: 0; background-color: #f5f0e8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; }
.email-wrapper { background-color: #f5f0e8; padding: 32px 16px; }

/* Container */
.email-container { max-width: 600px; margin: 0 auto; }

/* Header */
.email-header {
    background: #0D3822;
    border-radius: 16px 16px 0 0;
    padding: 28px 40px;
    text-align: center;
}
.email-logo {
    font-size: 22px; font-weight: 800;
    color: #ffffff; letter-spacing: .04em;
    text-decoration: none;
}
.email-logo span { color: #D4A20F; }

/* Hero band */
.email-hero {
    background: linear-gradient(135deg, #0D3822 0%, #1F6B44 100%);
    padding: 40px 40px 36px;
    text-align: center;
}
.email-hero-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(212,162,15,.2);
    border: 2px solid rgba(212,162,15,.4);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 28px; margin-bottom: 18px;
    /* Fallback pour clients qui supportent inline-flex */
    line-height: 64px; text-align: center;
}
.email-hero-title {
    font-size: 26px; font-weight: 700;
    color: #ffffff; line-height: 1.2;
    margin: 0 0 10px;
}
.email-hero-title em { color: #D4A20F; font-style: italic; }
.email-hero-sub {
    font-size: 15px; color: rgba(255,255,255,.7);
    line-height: 1.6; margin: 0;
}

/* Body */
.email-body {
    background: #ffffff;
    padding: 40px 40px 32px;
}
.email-greeting {
    font-size: 16px; color: #1F3A2A; font-weight: 600;
    margin: 0 0 20px;
}
.email-p {
    font-size: 15px; line-height: 1.75;
    color: #5a4a3a; margin: 0 0 16px;
}
.email-p strong { color: #1F3A2A; }

/* Card récap */
.email-card {
    background: #f9f5eb;
    border: 1.5px solid #e8dfc8;
    border-left: 4px solid #D4A20F;
    border-radius: 12px;
    padding: 20px 22px;
    margin: 24px 0;
}
.email-card-title {
    font-size: 10px; font-weight: 800;
    letter-spacing: .12em; text-transform: uppercase;
    color: #c49a0d; margin-bottom: 14px;
}
.email-row {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 8px 0; border-bottom: 1px solid #e8dfc8;
    font-size: 14px;
}
.email-row:last-child { border-bottom: none; padding-bottom: 0; }
.email-row-label { color: #9a8a78; font-weight: 500; }
.email-row-value { color: #1F3A2A; font-weight: 700; text-align: right; max-width: 60%; }

/* CTA button */
.email-cta-wrap { text-align: center; margin: 28px 0; }
.email-cta {
    display: inline-block;
    padding: 14px 36px;
    background: #D4A20F;
    color: #ffffff !important;
    text-decoration: none;
    border-radius: 50px;
    font-size: 15px; font-weight: 700;
    letter-spacing: .02em;
    mso-padding-alt: 14px 36px;
}

/* Alert box */
.email-alert {
    border-radius: 10px; padding: 14px 18px;
    margin: 20px 0; font-size: 13px; line-height: 1.65;
}
.email-alert--green { background: rgba(42,143,94,.08); border: 1px solid rgba(42,143,94,.25); color: #1F6B44; }
.email-alert--amber { background: rgba(212,162,15,.1); border: 1px solid rgba(212,162,15,.3); color: #8a6a00; }
.email-alert--red   { background: rgba(201,57,35,.07); border: 1px solid rgba(201,57,35,.2); color: #b03520; }

/* Divider */
.email-divider { height: 1px; background: #e8dfc8; margin: 24px 0; }

/* Footer */
.email-footer {
    background: #0D3822;
    border-radius: 0 0 16px 16px;
    padding: 28px 40px;
    text-align: center;
}
.email-footer-links { margin-bottom: 14px; }
.email-footer-links a {
    color: rgba(255,255,255,.55);
    text-decoration: none;
    font-size: 12px; font-weight: 500;
    margin: 0 8px;
}
.email-footer-links a:hover { color: #D4A20F; }
.email-footer-copy {
    font-size: 11px; color: rgba(255,255,255,.3);
    line-height: 1.6; margin: 0;
}
.email-footer-copy a { color: rgba(255,255,255,.4); text-decoration: none; }

/* Responsive */
@media (max-width: 600px) {
    .email-header, .email-hero, .email-body, .email-footer { padding-left: 22px !important; padding-right: 22px !important; }
    .email-hero-title { font-size: 22px !important; }
    .email-row { flex-direction: column; gap: 2px; }
    .email-row-value { text-align: left; max-width: 100%; }
}
</style>
</head>
<body>

{{-- Preheader invisible --}}
@if(isset($preheader))
<div style="display:none;font-size:1px;color:#f5f0e8;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
    {{ $preheader }}
</div>
@endif

<div class="email-wrapper">
<div class="email-container">

    {{-- Header logo --}}
    <div class="email-header">
        <a href="{{ config('app.url') }}" class="email-logo">
            Discov<span>Trip</span>
        </a>
    </div>

    {{-- Contenu injecté --}}
    {{ $slot }}

    {{-- Footer --}}
    <div class="email-footer">
        <div class="email-footer-links">
            <a href="{{ route('home') }}">Accueil</a>
            <a href="{{ route('destinations') }}">Destinations</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('faq') }}">FAQ</a>
            <a href="{{ route('privacy') }}">Confidentialité</a>
        </div>
        <p class="email-footer-copy">
            © {{ date('Y') }} DiscovTrip · Cotonou, Bénin<br>
            Vous recevez cet email car vous êtes inscrit sur DiscovTrip.<br>
            <a href="{{ route('account.profile') }}">Gérer mes préférences</a>
        </p>
    </div>

</div>
</div>
</body>
</html>