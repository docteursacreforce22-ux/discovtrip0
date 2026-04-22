<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur · DiscovTrip</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #F9F5EB; --cream-2: #F2EDD9; --ink-3: #1A1A17;
            --tx-primary: #1C1B16; --tx-mid: #3C3A2E; --tx-soft: #7A7868; --tx-muted: #AEADB2;
            --a-400: #E8BC3A; --a-500: #D4A20F;
            --b-300: #E87060; --b-500: #D94B2B;
            --bd-light: rgba(28,27,22,.09);
            --font-display: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Syne', sans-serif;
        }
        html, body { height: 100%; background: var(--cream); color: var(--tx-primary); font-family: var(--font-body); -webkit-font-smoothing: antialiased; }
        body { display: flex; flex-direction: column; min-height: 100vh; }

        .err-bg { position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 0; }
        .err-bg-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(212,162,15,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(212,162,15,.04) 1px, transparent 1px); background-size: 80px 80px; }
        .err-bg-c1 { position: absolute; width: 600px; height: 600px; top: -160px; right: -100px; border-radius: 50%; background: radial-gradient(circle, rgba(217,75,43,.05) 0%, transparent 70%); }
        .err-bg-c2 { position: absolute; width: 450px; height: 450px; bottom: -80px; left: -80px; border-radius: 50%; background: radial-gradient(circle, rgba(212,162,15,.06) 0%, transparent 70%); }

        .err-main { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 24px; text-align: center; }

        .err-logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 56px; opacity: .7; transition: opacity .2s; }
        .err-logo:hover { opacity: 1; }
        .err-logo-text { font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; color: var(--tx-primary); }

        .err-number { font-family: var(--font-display); font-size: clamp(96px, 20vw, 172px); font-weight: 300; line-height: 1; color: transparent; -webkit-text-stroke: 1.5px rgba(217,75,43,.14); letter-spacing: -.02em; user-select: none; margin-bottom: 6px; }

        .err-orn { display: flex; align-items: center; gap: 16px; margin-bottom: 26px; color: var(--b-300); }
        .err-orn-line { width: 48px; height: 1px; }
        .err-orn-line:first-child { background: linear-gradient(90deg, transparent, var(--b-300)); }
        .err-orn-line:last-child  { background: linear-gradient(90deg, var(--b-300), transparent); }

        .err-title { font-family: var(--font-display); font-size: clamp(1.5rem, 4vw, 2.3rem); font-weight: 600; margin-bottom: 14px; line-height: 1.2; }
        .err-desc  { font-size: .93rem; color: var(--tx-soft); max-width: 420px; line-height: 1.72; margin-bottom: 38px; }

        .err-actions { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: center; }
        .err-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 26px; border-radius: 100px; font-family: var(--font-body); font-size: .875rem; font-weight: 700; text-decoration: none; letter-spacing: .03em; transition: all .2s; white-space: nowrap; cursor: pointer; border: none; }
        .err-btn--primary { background: var(--a-400); color: var(--ink-3); box-shadow: 0 4px 16px rgba(212,162,15,.32); }
        .err-btn--primary:hover { background: var(--a-500); transform: translateY(-1px); }
        .err-btn--ghost { background: transparent; color: var(--tx-mid); border: 1.5px solid var(--bd-light); }
        .err-btn--ghost:hover { background: var(--cream-2); border-color: rgba(28,27,22,.18); }

        .err-contact { margin-top: 32px; font-size: .82rem; color: var(--tx-muted); }
        .err-contact a { color: var(--a-500); text-decoration: none; font-weight: 600; }
        .err-contact a:hover { text-decoration: underline; }

        .err-footer { position: relative; z-index: 1; padding: 20px 24px; text-align: center; font-size: .78rem; color: var(--tx-muted); border-top: 1px solid var(--bd-light); }

        @media (max-width: 480px) { .err-actions { flex-direction: column; width: 100%; } .err-btn { justify-content: center; } }
    </style>
</head>
<body>

<div class="err-bg">
    <div class="err-bg-grid"></div>
    <div class="err-bg-c1"></div>
    <div class="err-bg-c2"></div>
</div>

<main class="err-main">

    <a href="{{ route('home') }}" class="err-logo">
        <svg width="34" height="34" viewBox="0 0 40 40" fill="none">
            <circle cx="20" cy="20" r="19" stroke="#E8BC3A" stroke-width="1.5"/>
            <path d="M12 28 L20 10 L28 28" stroke="#E8BC3A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14.5 22 L25.5 22" stroke="#E8BC3A" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="err-logo-text">DiscovTrip</span>
    </a>

    <div class="err-number">500</div>

    <div class="err-orn">
        <span class="err-orn-line"></span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#D94B2B" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span class="err-orn-line"></span>
    </div>

    <h1 class="err-title">Une erreur inattendue s'est produite</h1>

    <p class="err-desc">
        Nous travaillons à résoudre le problème.
        Réessayez dans quelques instants ou revenez à l'accueil.
    </p>

    <div class="err-actions">
        <a href="{{ route('home') }}" class="err-btn err-btn--primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Retour à l'accueil
        </a>
        <button onclick="window.location.reload()" class="err-btn err-btn--ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Réessayer
        </button>
    </div>

    <p class="err-contact">
        Si le problème persiste,
        <a href="{{ route('contact') }}">contactez notre équipe</a>.
    </p>

</main>

<footer class="err-footer">
    &copy; {{ date('Y') }} DiscovTrip — Tourisme premium au Bénin
</footer>

</body>
</html>