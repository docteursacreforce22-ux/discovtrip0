<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable · DiscovTrip</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream:        #F9F5EB;
            --cream-2:      #F2EDD9;
            --ink-3:        #1A1A17;
            --tx-primary:   #1C1B16;
            --tx-mid:       #3C3A2E;
            --tx-soft:      #7A7868;
            --tx-muted:     #AEADB2;
            --a-400:        #E8BC3A;
            --a-500:        #D4A20F;
            --bd-light:     rgba(28,27,22,.09);
            --font-display: 'Cormorant Garamond', Georgia, serif;
            --font-body:    'Syne', sans-serif;
        }

        html, body { height: 100%; background: var(--cream); color: var(--tx-primary); font-family: var(--font-body); -webkit-font-smoothing: antialiased; }
        body { display: flex; flex-direction: column; min-height: 100vh; }

        .err-bg { position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 0; }
        .err-bg-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(212,162,15,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(212,162,15,.04) 1px, transparent 1px); background-size: 80px 80px; }
        .err-bg-c1 { position: absolute; width: 700px; height: 700px; top: -200px; right: -150px; border-radius: 50%; background: radial-gradient(circle, rgba(212,162,15,.08) 0%, transparent 70%); }
        .err-bg-c2 { position: absolute; width: 500px; height: 500px; bottom: -100px; left: -100px; border-radius: 50%; background: radial-gradient(circle, rgba(42,143,94,.06) 0%, transparent 70%); }

        .err-main { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 24px; text-align: center; }

        .err-logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 56px; opacity: .7; transition: opacity .2s; }
        .err-logo:hover { opacity: 1; }
        .err-logo-text { font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; color: var(--tx-primary); }

        .err-number { font-family: var(--font-display); font-size: clamp(96px, 20vw, 172px); font-weight: 300; line-height: 1; color: transparent; -webkit-text-stroke: 1.5px rgba(28,27,22,.11); letter-spacing: -.02em; user-select: none; margin-bottom: 6px; }

        .err-orn { display: flex; align-items: center; gap: 16px; margin-bottom: 26px; color: var(--a-500); }
        .err-orn-line { width: 48px; height: 1px; }
        .err-orn-line:first-child { background: linear-gradient(90deg, transparent, var(--a-400)); }
        .err-orn-line:last-child  { background: linear-gradient(90deg, var(--a-400), transparent); }

        .err-title { font-family: var(--font-display); font-size: clamp(1.5rem, 4vw, 2.3rem); font-weight: 600; margin-bottom: 14px; line-height: 1.2; }
        .err-desc  { font-size: .93rem; color: var(--tx-soft); max-width: 400px; line-height: 1.72; margin-bottom: 38px; }

        .err-actions { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: center; }
        .err-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 26px; border-radius: 100px; font-family: var(--font-body); font-size: .875rem; font-weight: 700; text-decoration: none; letter-spacing: .03em; transition: all .2s; white-space: nowrap; }
        .err-btn--primary { background: var(--a-400); color: var(--ink-3); box-shadow: 0 4px 16px rgba(212,162,15,.32); }
        .err-btn--primary:hover { background: var(--a-500); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(212,162,15,.44); }
        .err-btn--ghost { background: transparent; color: var(--tx-mid); border: 1.5px solid var(--bd-light); }
        .err-btn--ghost:hover { background: var(--cream-2); border-color: rgba(28,27,22,.18); }

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

    <div class="err-number">404</div>

    <div class="err-orn">
        <span class="err-orn-line"></span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="#D4A20F"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
        <span class="err-orn-line"></span>
    </div>

    <h1 class="err-title">Cette page s'est perdue en chemin</h1>

    <p class="err-desc">
        La page que vous cherchez n'existe pas ou a été déplacée.
        Vos prochaines aventures au Bénin vous attendent.
    </p>

    <div class="err-actions">
        <a href="{{ route('home') }}" class="err-btn err-btn--primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Retour à l'accueil
        </a>
        <a href="{{ route('offers.index') }}" class="err-btn err-btn--ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Explorer les expériences
        </a>
    </div>

</main>

<footer class="err-footer">
    &copy; {{ date('Y') }} DiscovTrip — Tourisme premium au Bénin
</footer>

</body>
</html>