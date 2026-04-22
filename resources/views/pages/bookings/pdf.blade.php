<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de réservation {{ $booking->reference }} — DiscovTrip</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Syne:wght@400;500;600;700;800&family=Inconsolata:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
    /* ── Fix impression couleurs ─────────────────── */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0; padding: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    :root {
        --f-900:  #0f1f14;
        --f-700:  #1C4F32;
        --f-500:  #2d7a4f;
        --f-400:  #4a9a6a;
        --f-300:  #a8d5b8;
        --f-100:  #e8f5ee;

        --a-600:  #9a6210;
        --a-500:  #b47519;
        --a-400:  #c9882a;
        --a-300:  #dba84a;
        --a-100:  #fdf3e3;

        --cream:  #faf7f2;
        --cream-2:#f5efe6;
        --cream-3:#ede3d5;

        --tx-pri: #1a2e1f;
        --tx-mid: #3d5c47;
        --tx-soft:#7a9e87;
        --tx-mut: #b0c9b8;

        --bd:     rgba(28,79,50,.15);
        --bd-lt:  rgba(28,79,50,.08);

        --font-d: 'Cormorant Garamond', serif;
        --font-b: 'Syne', sans-serif;
        --font-m: 'Inconsolata', monospace;
    }

    /* ── BODY ─────────────────────────────────────── */
    body {
        font-family: var(--font-b);
        background: var(--cream-3);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: var(--tx-pri);
    }

    /* ── TOOLBAR ──────────────────────────────────── */
    .toolbar {
        width: 100%;
        background: var(--f-900);
        padding: .75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 20px rgba(0,0,0,.35);
    }
    .toolbar-brand {
        font-family: var(--font-d);
        font-size: .95rem;
        font-weight: 600;
        color: rgba(255,255,255,.5);
        letter-spacing: .03em;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .toolbar-brand strong { color: rgba(255,255,255,.85); }
    .toolbar-brand span   { color: rgba(255,255,255,.2); }
    .toolbar-actions { display: flex; gap: .5rem; }
    .toolbar-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem 1.1rem;
        border-radius: .5rem;
        font-size: .78rem;
        font-weight: 700;
        cursor: pointer;
        font-family: var(--font-b);
        text-decoration: none;
        border: none;
        transition: opacity .2s, transform .15s;
        letter-spacing: .02em;
    }
    .toolbar-btn:hover { opacity: .85; transform: translateY(-1px); }
    .toolbar-btn--print { background: var(--a-500); color: #fff; }
    .toolbar-btn--back  { background: rgba(255,255,255,.1); color: rgba(255,255,255,.6); border: 1px solid rgba(255,255,255,.15); }

    /* ── DOC OUTER ────────────────────────────────── */
    .doc-outer {
        width: 100%;
        max-width: 820px;
        padding: 2rem 1.25rem 4rem;
    }

    /* ── VOUCHER ──────────────────────────────────── */
    .voucher {
        background: var(--cream);
        border-radius: .5rem;
        /* overflow:hidden retiré — il bloquait le tampon position:absolute */
        position: relative;
        box-shadow:
            0 0 0 1px rgba(28,79,50,.12),
            0 4px 6px rgba(0,0,0,.05),
            0 24px 70px rgba(28,79,50,.18);
    }
    /* Coins arrondis appliqués sur premier et dernier enfant */
    .v-topband             { border-radius: .5rem .5rem 0 0; }
    .v-footer              { border-radius: 0 0 .5rem .5rem; }

    /* ── Filigrane diagonal ──────────────────────── */
    .v-watermark {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
    }
    .v-watermark-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-28deg);
        font-family: var(--font-d);
        font-size: 7rem;
        font-weight: 700;
        color: var(--f-700);
        opacity: .022;
        white-space: nowrap;
        letter-spacing: .2em;
        text-transform: uppercase;
        user-select: none;
    }

    /* ── Bande supérieure ambre (accent premium) ─── */
    .v-topband {
        height: 4px;
        background: linear-gradient(90deg, var(--f-700) 0%, var(--a-500) 50%, var(--f-700) 100%);
        flex-shrink: 0;
    }

    /* ════ HEADER ════ */
    .v-header {
        position: relative;
        z-index: 1;
        background: var(--cream-2);
        border-bottom: 2px solid var(--f-700);
        padding: 1.75rem 2.25rem 1.5rem;
        overflow: hidden;
    }
    /* Motif géométrique africain */
    .v-header::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .038;
        background-image:
            repeating-linear-gradient(45deg,  var(--f-700) 0, var(--f-700) 1px, transparent 0, transparent 50%),
            repeating-linear-gradient(-45deg, var(--f-700) 0, var(--f-700) 1px, transparent 0, transparent 50%);
        background-size: 18px 18px;
    }
    /* Bande latérale gauche */
    .v-header::after {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 5px;
        background: linear-gradient(to bottom, var(--f-700), var(--a-500));
    }
    .v-header-inner {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
    }

    /* Logo */
    .v-logo-block { display: flex; flex-direction: column; gap: 0; }
    .v-logo-img {
        height: 88px;
        width: auto;
        object-fit: contain;
        display: block;
    }
    .v-logo-fallback {
        display: none;
        font-family: var(--font-d);
        font-size: 2rem;
        font-weight: 700;
        color: var(--f-700);
        letter-spacing: -.01em;
        line-height: 1;
    }
    .v-logo-tagline {
        font-family: var(--font-b);
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--tx-soft);
        margin-top: .45rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .v-logo-tagline::before {
        content: '';
        width: 14px; height: 1.5px;
        background: var(--a-500);
        border-radius: 2px;
        flex-shrink: 0;
    }

    /* Doc meta (droite) */
    .v-doc-meta { text-align: right; flex-shrink: 0; }
    .v-doc-type {
        font-family: var(--font-b);
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--a-500);
        margin-bottom: .4rem;
    }
    .v-ref {
        font-family: var(--font-m);
        font-size: 1.35rem;
        font-weight: 600;
        color: var(--tx-pri);
        letter-spacing: .08em;
        line-height: 1;
    }
    .v-issued {
        font-family: var(--font-b);
        font-size: .65rem;
        color: var(--tx-soft);
        margin-top: .35rem;
    }

    /* Status strip */
    .v-status-strip {
        position: relative;
        z-index: 2;
        margin-top: 1.1rem;
        padding-top: .875rem;
        border-top: 1px solid var(--bd);
        display: flex;
        align-items: center;
        gap: .65rem;
        flex-wrap: wrap;
    }
    .v-status-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .9rem;
        border-radius: 2rem;
        font-family: var(--font-b);
        font-size: .7rem;
        font-weight: 700;
        border: 1.5px solid;
    }
    .v-status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .vpill--confirmed { color: var(--f-700); background: rgba(28,79,50,.09); border-color: rgba(28,79,50,.28); }
    .vpill--pending   { color: var(--a-600); background: rgba(180,117,26,.09); border-color: rgba(180,117,26,.28); }
    .vpill--cancelled { color: #9B1C1C;      background: rgba(155,28,28,.07); border-color: rgba(155,28,28,.2); }

    .v-paid-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-family: var(--font-b);
        font-size: .65rem;
        font-weight: 700;
        padding: .25rem .65rem;
        border-radius: 2rem;
        background: rgba(28,79,50,.1);
        color: var(--f-700);
        border: 1px solid rgba(28,79,50,.22);
    }
    .v-onsite-note {
        font-family: var(--font-b);
        font-size: .68rem;
        color: var(--tx-soft);
        font-style: italic;
    }
    .v-status-note {
        margin-left: auto;
        font-family: var(--font-b);
        font-size: .68rem;
        color: var(--tx-soft);
        font-style: italic;
    }

    /* ════ PHOTO DE L'OFFRE ════ */
    /* background-image permet object-position complet + pas de déformation */
    .v-photo-wrap {
        width: 100%;
        height: 160px;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    .v-offer-photo-bg {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center 35%; /* favorise le haut pour les paysages */
        background-repeat: no-repeat;
        background-color: var(--f-700);
        transition: none;
    }
    .v-offer-photo-ph {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--f-700), var(--f-500));
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Overlay gradient bas pour lisibilité */
    .v-photo-wrap::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 70px;
        background: linear-gradient(to top, rgba(15,31,20,.6), transparent);
        pointer-events: none;
        z-index: 2;
    }

    /* ════ PERFORATION ════ */
    .v-perf {
        height: 22px;
        background: var(--cream-3);
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
    }
    .v-perf::before,
    .v-perf::after {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 22px; height: 22px;
        border-radius: 50%;
        background: var(--cream-3);
    }
    .v-perf::before { left: -11px; }
    .v-perf::after  { right: -11px; }
    .v-perf-line {
        flex: 1;
        margin: 0 1.5rem;
        border-top: 2px dashed var(--bd);
    }

    /* ════ BODY ════ */
    .v-body {
        padding: 1.75rem 2.25rem 2rem;
        position: relative;
        z-index: 1;
    }

    /* ── Bloc offre ───────────────────────────────── */
    .v-offer {
        display: flex;
        gap: 1rem;
        align-items: center;
        background: linear-gradient(135deg, var(--f-100), rgba(253,243,227,.45));
        border: 1.5px solid var(--bd);
        border-radius: .75rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .v-offer::after {
        content: '';
        position: absolute;
        right: -14px; top: -14px;
        width: 88px; height: 88px;
        border-radius: 50%;
        background: rgba(28,79,50,.05);
        pointer-events: none;
    }
    .v-offer-icon {
        width: 44px; height: 44px;
        border-radius: .6rem;
        flex-shrink: 0;
        background: var(--f-700);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(28,79,50,.3);
    }
    .v-offer-location {
        font-family: var(--font-b);
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--a-500);
        margin-bottom: .3rem;
    }
    .v-offer-title {
        font-family: var(--font-d);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--tx-pri);
        line-height: 1.25;
    }
    .v-offer-meta {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: wrap;
        margin-top: .4rem;
    }
    .v-offer-tier {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-family: var(--font-b);
        font-size: .65rem;
        font-weight: 700;
        color: var(--a-500);
        background: rgba(180,117,26,.1);
        padding: .15rem .55rem;
        border-radius: 2rem;
    }
    .v-offer-duration {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-family: var(--font-b);
        font-size: .65rem;
        font-weight: 600;
        color: var(--tx-soft);
    }

    /* ── Grilles ──────────────────────────────────── */
    .v-grid   { display: grid; gap: .5rem; margin-bottom: .5rem; }
    .v-grid-2 { grid-template-columns: 1fr 1fr; }
    .v-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .v-grid-mb { margin-bottom: 1.25rem; }

    .v-cell {
        background: var(--cream-2);
        border: 1px solid var(--bd-lt);
        border-radius: .6rem;
        padding: .75rem .875rem;
    }
    .v-cell-lbl {
        display: flex;
        align-items: center;
        gap: .35rem;
        font-family: var(--font-b);
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--a-500);
        margin-bottom: .35rem;
    }
    .v-cell-lbl svg { opacity: .75; flex-shrink: 0; }
    .v-cell-val {
        font-family: var(--font-b);
        font-size: .83rem;
        font-weight: 700;
        color: var(--tx-pri);
        line-height: 1.35;
    }
    .v-cell-sub {
        font-family: var(--font-b);
        font-size: .65rem;
        color: var(--tx-soft);
        margin-top: .2rem;
    }

    /* ── Séparateur ornemental ────────────────────── */
    .v-sep {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 1.1rem 0 .875rem;
    }
    .v-sep-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, transparent, var(--bd), transparent);
    }
    .v-sep-ornament {
        display: flex;
        align-items: center;
        gap: .35rem;
        flex-shrink: 0;
    }
    .v-sep-text {
        font-family: var(--font-b);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--a-500);
    }

    /* ── Prix ─────────────────────────────────────── */
    .v-pricing {
        background: var(--cream-2);
        border: 1.5px solid var(--bd);
        border-radius: .75rem;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .v-pricing-rows { padding: .875rem 1.1rem; display: flex; flex-direction: column; }
    .v-pr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .45rem 0;
        border-bottom: 1px dashed var(--bd-lt);
        font-family: var(--font-b);
        font-size: .8rem;
        color: var(--tx-mid);
        font-weight: 500;
    }
    .v-pr:last-of-type { border-bottom: none; }
    .v-pr span:last-child { font-family: var(--font-m); font-size: .76rem; font-weight: 600; }
    .v-total-band {
        background: var(--f-700);
        padding: 1rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .v-total-lbl {
        font-family: var(--font-b);
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgba(255,255,255,.5);
    }
    .v-total-note { font-family: var(--font-b); font-size: .62rem; color: rgba(255,255,255,.35); margin-top: .15rem; }
    .v-total-amount {
        font-family: var(--font-d);
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--a-300);
        line-height: 1;
    }
    .v-total-eur {
        font-family: var(--font-b);
        font-size: .65rem;
        color: rgba(255,255,255,.3);
        text-align: right;
        margin-top: .2rem;
    }

    /* ── Conditions ───────────────────────────────── */
    .v-conditions {
        background: rgba(180,117,26,.04);
        border: 1px solid rgba(180,117,26,.2);
        border-left: 3px solid var(--a-500);
        border-radius: 0 .6rem .6rem 0;
        padding: .875rem 1rem;
        font-family: var(--font-b);
        font-size: .76rem;
        color: var(--tx-mid);
        line-height: 1.8;
        font-weight: 500;
    }
    .v-conditions ul { padding-left: 1rem; }
    .v-conditions li { margin-bottom: .05rem; }
    .v-conditions strong { color: var(--f-700); font-weight: 700; }

    /* ── Notes client ─────────────────────────────── */
    .v-notes {
        background: rgba(28,79,50,.04);
        border: 1px dashed var(--bd);
        border-radius: .6rem;
        padding: .75rem 1rem;
        font-family: var(--font-b);
        font-size: .8rem;
        color: var(--tx-mid);
        line-height: 1.65;
        font-style: italic;
        margin-bottom: 1rem;
    }

    /* ── Zone QR + Tampon ─────────────────────────── */
    .v-bottom-row {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        margin-top: 1.35rem;
        padding-top: 1.1rem;
        border-top: 1px solid var(--bd);
    }
    /* QR code */
    .v-qr-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .4rem;
        flex-shrink: 0;
    }
    .v-qr-wrap {
        width: 80px; height: 80px;
        background: #fff;
        border: 1.5px solid var(--bd);
        border-radius: .5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        /* PAS d'overflow:hidden — le QR div généré doit avoir de la place */
    }
    /* qrcodejs génère un div > img ou div > canvas à l'intérieur */
    #v-qr-container img,
    #v-qr-container canvas {
        display: block !important;
        width: 72px !important;
        height: 72px !important;
    }
    .v-qr-label {
        font-family: var(--font-b);
        font-size: .58rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--tx-soft);
        text-align: center;
    }
    /* Guide */
    .v-guide-block {
        flex: 1;
        min-width: 0;
    }
    .v-guide-lbl {
        font-family: var(--font-b);
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--a-500);
        margin-bottom: .5rem;
    }
    .v-guide-row {
        display: flex;
        align-items: center;
        gap: .65rem;
    }
    .v-guide-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--f-300);
        flex-shrink: 0;
    }
    .v-guide-avatar-ph {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: var(--f-300);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .v-guide-name {
        font-family: var(--font-b);
        font-size: .82rem;
        font-weight: 700;
        color: var(--tx-pri);
    }
    .v-guide-type {
        font-family: var(--font-b);
        font-size: .65rem;
        color: var(--tx-soft);
        margin-top: .1rem;
    }
    .v-guide-pending {
        font-family: var(--font-b);
        font-size: .78rem;
        color: var(--tx-soft);
        font-style: italic;
        padding: .5rem .75rem;
        background: var(--cream-2);
        border-radius: .5rem;
        border: 1px dashed var(--bd);
    }

    /* ── Tampon CONFIRMÉ — CSS pur (SVG text non fiable sans font embarquée) ── */
    .v-stamp {
        position: absolute;
        top: 1.75rem;
        right: 2rem;
        width: 96px;
        height: 96px;
        pointer-events: none;
        z-index: 10;
        transform: rotate(-18deg);
    }
    .v-stamp-circle {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        border: 3px solid var(--f-700);
        box-shadow: inset 0 0 0 5px rgba(28,79,50,.12), 0 0 0 1.5px rgba(28,79,50,.18);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        opacity: .55;
        background: rgba(28,79,50,.04);
    }
    .v-stamp-main {
        font-family: var(--font-b);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .16em;
        color: var(--f-700);
        text-transform: uppercase;
        line-height: 1;
    }
    .v-stamp-line {
        width: 40px;
        height: 1px;
        background: var(--f-700);
        opacity: .5;
        margin: 2px 0;
    }
    .v-stamp-sub {
        font-family: var(--font-b);
        font-size: .52rem;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--f-700);
        text-transform: uppercase;
        line-height: 1;
        opacity: .7;
    }

    /* ════ FOOTER ════ */
    .v-footer {
        background: var(--cream-2);
        border-top: 1.5px solid var(--bd);
        padding: 1rem 2.25rem;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 1.25rem;
        position: relative;
        z-index: 1;
    }
    .v-footer-left { display: flex; align-items: center; gap: .75rem; }
    .v-footer-divider { width: 1px; height: 28px; background: var(--bd); flex-shrink: 0; }
    .v-footer-brand {
        font-family: var(--font-d);
        font-size: .88rem;
        font-weight: 700;
        color: var(--f-700);
    }
    .v-footer-sub {
        font-family: var(--font-b);
        font-size: .6rem;
        color: var(--tx-soft);
        margin-top: .1rem;
        font-weight: 600;
        letter-spacing: .06em;
    }
    .v-footer-tagline {
        font-family: var(--font-b);
        font-size: .65rem;
        color: var(--tx-soft);
        line-height: 1.7;
        font-weight: 500;
    }
    .v-footer-center {
        text-align: center;
        font-family: var(--font-b);
        font-size: .7rem;
        color: var(--tx-soft);
        line-height: 1.85;
        font-weight: 500;
    }
    .v-footer-right { text-align: right; }
    .v-footer-ref-lbl {
        font-family: var(--font-b);
        font-size: .58rem;
        color: var(--tx-mut);
        text-transform: uppercase;
        letter-spacing: .1em;
        margin-bottom: .2rem;
        font-weight: 700;
    }
    .v-footer-ref-code {
        font-family: var(--font-m);
        font-size: .82rem;
        font-weight: 600;
        color: var(--a-500);
    }
    .v-footer-url {
        font-family: var(--font-b);
        font-size: .6rem;
        color: var(--tx-mut);
        margin-top: .1rem;
    }

    /* ── PRINT ────────────────────────────────────── */
    @media print {
        body { background: white; }
        .toolbar { display: none !important; }
        .doc-outer { padding: 0; max-width: 100%; }
        .voucher { box-shadow: none; border-radius: 0; }
        .v-perf { background: white; }
        .v-perf::before, .v-perf::after { background: white; }
        .v-grid-2, .v-grid-3 { display: grid !important; }
        .v-grid-2 { grid-template-columns: 1fr 1fr !important; }
        .v-grid-3 { grid-template-columns: 1fr 1fr 1fr !important; }
        @page { margin: 1cm; size: A4; }
    }
    </style>
</head>
<body>

@php
    $participants = $booking->participants ?? 1;
    $isPaid       = $booking->is_paid || ($booking->payment_status ?? '') === 'paid';
    $isOnSite     = ($booking->payment_method ?? 'on_site') === 'on_site';

    $statusMap = [
        'confirmed'            => ['pill' => 'vpill--confirmed', 'label' => 'Réservation confirmée',     'note' => 'Votre place est assurée ✓'],
        'pending'              => ['pill' => 'vpill--pending',   'label' => 'En attente de confirmation', 'note' => 'Confirmation sous 24h'],
        'cancelled_by_user'    => ['pill' => 'vpill--cancelled', 'label' => 'Annulée',                    'note' => ''],
        'cancelled_by_partner' => ['pill' => 'vpill--cancelled', 'label' => 'Annulée',                    'note' => ''],
    ];
    $st = $statusMap[$booking->status] ?? $statusMap['pending'];

    $clientName  = auth()->check()
        ? trim((auth()->user()->first_name ?? '') . ' ' . (auth()->user()->last_name ?? auth()->user()->name ?? ''))
        : trim(($booking->guest_first_name ?? '') . ' ' . ($booking->guest_last_name ?? ''));
    $clientEmail = auth()->check() ? auth()->user()->email : ($booking->guest_email ?? '');
    $clientPhone = auth()->check() ? (auth()->user()->phone ?? '—') : ($booking->guest_phone ?? '—');

    $unitPrice = $booking->total_price / ($participants ?: 1);

    // Guide
    $offer        = $booking->offer;
    $showGuide    = $offer->show_guide_profile ?? false;
    $guideUser    = $offer->user ?? null;
    $guideTypeLabel = $offer->guide_type_label ?? 'Guide';

    $backUrl = route('bookings.show', $booking->reference);
    if ($booking->guest_email) $backUrl .= '?email=' . urlencode($booking->guest_email);

    $bookingUrl = url('/bookings/' . $booking->reference);
@endphp

{{-- ── TOOLBAR ─────────────────────────────────── --}}
<div class="toolbar">
    <div class="toolbar-brand">
        <strong>DiscovTrip</strong>
        <span>·</span>
        Bon de réservation
        <span>·</span>
        {{ $booking->reference }}
    </div>
    <div class="toolbar-actions">
        <a href="{{ $backUrl }}" class="toolbar-btn toolbar-btn--back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Retour
        </a>
        <button onclick="window.print()" class="toolbar-btn toolbar-btn--print">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Imprimer / Enregistrer PDF
        </button>
    </div>
</div>

<div class="doc-outer">
<div class="voucher">

    {{-- ── Filigrane ── --}}
    <div class="v-watermark" aria-hidden="true">
        <div class="v-watermark-text">DiscovTrip</div>
    </div>

    {{-- ── Tampon CONFIRMÉ (si statut confirmé) ── --}}
    @if($booking->status === 'confirmed')
        <div class="v-stamp" aria-hidden="true">
            <div class="v-stamp-circle">
                <div class="v-stamp-main">Confirmé</div>
                <div class="v-stamp-line"></div>
                <div class="v-stamp-sub">DiscovTrip</div>
            </div>
        </div>
    @endif

    {{-- ── Bande premium supérieure ── --}}
    <div class="v-topband"></div>

    {{-- ════ HEADER ════ --}}
    <div class="v-header">
        <div class="v-header-inner">

            {{-- Logo --}}
            <div class="v-logo-block">
                <div class="v-logo-img-wrap">
                    <img src="{{ asset('images/logo.jpg') }}"
                         alt="DiscovTrip"
                         class="v-logo-img"
                         onerror="this.parentElement.style.display='none';document.getElementById('logo-fb').style.display='block'">
                </div>
                <div id="logo-fb" class="v-logo-fallback">DiscovTrip</div>
                <div class="v-logo-tagline">L'Afrique Autrement · Bénin 🇧🇯</div>
            </div>

            {{-- Référence --}}
            <div class="v-doc-meta">
                <div class="v-doc-type">Bon de réservation</div>
                <div class="v-ref">{{ $booking->reference }}</div>
                <div class="v-issued">
                    Émis le {{ \Carbon\Carbon::parse($booking->created_at)->locale('fr')->isoFormat('D MMM YYYY [à] HH:mm') }}
                </div>
            </div>
        </div>

        {{-- Statut --}}
        <div class="v-status-strip">
            <div class="v-status-pill {{ $st['pill'] }}">
                <span class="v-status-dot"></span>
                {{ $st['label'] }}
            </div>
            @if($isPaid)
                <span class="v-paid-chip">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Payé
                </span>
            @elseif($isOnSite)
                <span class="v-onsite-note">Paiement sur place le jour J</span>
            @endif
            @if($st['note'])
                <span class="v-status-note">{{ $st['note'] }}</span>
            @endif
        </div>
    </div>

    {{-- ════ PHOTO DE L'OFFRE ════ --}}
    <div class="v-photo-wrap">
        @if($offer->cover_image)
            <div class="v-offer-photo-bg"
                 style="background-image: url('{{ Storage::url($offer->cover_image) }}');">
            </div>
        @else
            <div class="v-offer-photo-ph">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.35)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            </div>
        @endif
    </div>

    {{-- ════ PERFORATION ════ --}}
    <div class="v-perf"><div class="v-perf-line"></div></div>

    {{-- ════ BODY ════ --}}
    <div class="v-body">

        {{-- Titre offre --}}
        <div class="v-offer">
            <div class="v-offer-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="v-offer-location">📍 {{ $offer->city->name ?? 'Bénin' }}, Bénin</div>
                <div class="v-offer-title">{{ $offer->title }}</div>
                <div class="v-offer-meta">
                    @if($booking->tier)
                        <div class="v-offer-tier">{{ $booking->tier->emoji ?? '' }} {{ $booking->tier->label }}</div>
                    @endif
                    @if($offer->duration_formatted ?? false)
                        <div class="v-offer-duration">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                            {{ $offer->duration_formatted }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Date + Participants --}}
        <div class="v-grid v-grid-2">
            <div class="v-cell">
                <div class="v-cell-lbl">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Date de l'expérience
                </div>
                <div class="v-cell-val">
                    {{ \Carbon\Carbon::parse($booking->booking_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                </div>
                @if($booking->booking_time)
                    <div class="v-cell-sub">⏰ {{ $booking->booking_time }}</div>
                @endif
            </div>
            <div class="v-cell">
                <div class="v-cell-lbl">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Participants
                </div>
                <div class="v-cell-val">{{ $participants }} personne{{ $participants > 1 ? 's' : '' }}</div>
            </div>
        </div>

        {{-- Client --}}
        <div class="v-grid v-grid-3 v-grid-mb">
            <div class="v-cell">
                <div class="v-cell-lbl">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Client
                </div>
                <div class="v-cell-val">{{ $clientName ?: '—' }}</div>
            </div>
            <div class="v-cell">
                <div class="v-cell-lbl">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email
                </div>
                <div class="v-cell-val" style="font-size:.72rem;word-break:break-all;">{{ $clientEmail ?: '—' }}</div>
            </div>
            <div class="v-cell">
                <div class="v-cell-lbl">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.4 1.17 2 2 0 012 .18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.29-1.29a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    Téléphone
                </div>
                <div class="v-cell-val">{{ $clientPhone }}</div>
            </div>
        </div>

        {{-- Récapitulatif financier --}}
        <div class="v-sep">
            <div class="v-sep-line"></div>
            <div class="v-sep-ornament">
                <svg width="6" height="6" viewBox="0 0 10 10"><polygon points="5,0 10,5 5,10 0,5" fill="var(--a-500)"/></svg>
                <span class="v-sep-text">Récapitulatif financier</span>
                <svg width="6" height="6" viewBox="0 0 10 10"><polygon points="5,0 10,5 5,10 0,5" fill="var(--a-500)"/></svg>
            </div>
            <div class="v-sep-line"></div>
        </div>
        <div class="v-pricing">
            <div class="v-pricing-rows">
                <div class="v-pr">
                    <span>Prix unitaire</span>
                    <span>{{ number_format($unitPrice, 0, ',', ' ') }} FCFA / pers.</span>
                </div>
                <div class="v-pr">
                    <span>Participants</span>
                    <span>× {{ $participants }}</span>
                </div>
                @if($booking->tier)
                    <div class="v-pr">
                        <span>Formule sélectionnée</span>
                        <span>{{ $booking->tier->emoji ?? '' }} {{ $booking->tier->label }}</span>
                    </div>
                @endif
                <div class="v-pr">
                    <span>Mode de paiement</span>
                    <span>
                        @if($isOnSite) Sur place le jour J
                        @elseif($booking->payment_method === 'fedapay') Mobile Money (FedaPay)
                        @else Carte bancaire (Stripe)
                        @endif
                    </span>
                </div>
            </div>
            <div class="v-total-band">
                <div>
                    <div class="v-total-lbl">Montant total</div>
                    @if($isOnSite)
                        <div class="v-total-note">À régler le jour J</div>
                    @elseif($isPaid)
                        <div class="v-total-note">✓ Paiement reçu</div>
                    @endif
                </div>
                <div style="text-align:right;">
                    <div class="v-total-amount">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</div>
                    <div class="v-total-eur">≈ {{ number_format($booking->total_price / config('discovtrip.eur_rate', 655.957), 0, ',', ' ') }} €</div>
                </div>
            </div>
        </div>

        {{-- Message client --}}
        @if($booking->notes)
            <div class="v-sep">
                <div class="v-sep-line"></div>
                <div class="v-sep-ornament">
                    <svg width="6" height="6" viewBox="0 0 10 10"><polygon points="5,0 10,5 5,10 0,5" fill="var(--a-500)"/></svg>
                    <span class="v-sep-text">Message</span>
                    <svg width="6" height="6" viewBox="0 0 10 10"><polygon points="5,0 10,5 5,10 0,5" fill="var(--a-500)"/></svg>
                </div>
                <div class="v-sep-line"></div>
            </div>
            <div class="v-notes">"{{ $booking->notes }}"</div>
        @endif

        {{-- Conditions --}}
        <div class="v-sep">
            <div class="v-sep-line"></div>
            <div class="v-sep-ornament">
                <svg width="6" height="6" viewBox="0 0 10 10"><polygon points="5,0 10,5 5,10 0,5" fill="var(--a-500)"/></svg>
                <span class="v-sep-text">Conditions importantes</span>
                <svg width="6" height="6" viewBox="0 0 10 10"><polygon points="5,0 10,5 5,10 0,5" fill="var(--a-500)"/></svg>
            </div>
            <div class="v-sep-line"></div>
        </div>
        <div class="v-conditions">
            <ul>
                <li>Présentez ce bon (numérique ou imprimé) à votre guide le jour de l'expérience.</li>
                <li>Le point de rendez-vous sera communiqué par votre guide au moins <strong>24h avant</strong>.</li>
                <li>Annulation gratuite jusqu'à <strong>48h avant</strong> — passé ce délai, aucun remboursement.</li>
                <li>En cas de force majeure, DiscovTrip se réserve le droit de reprogrammer l'expérience.</li>
                <li>Contact : <strong><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="eb8884859f8a889fab8f829888849d9f99829bc5888486">[email&#160;protected]</a></strong> · WhatsApp : <strong>+229 01 91 09 43 66</strong></li>
            </ul>
        </div>

        {{-- QR code + Guide ──────────────────────── --}}
        <div class="v-bottom-row">

            {{-- QR code --}}
            <div class="v-qr-block">
                <div class="v-qr-wrap">
                    <div id="v-qr-container"></div>
                </div>
                <div class="v-qr-label">Scanner pour<br>vérifier</div>
            </div>

            {{-- Guide --}}
            <div class="v-guide-block">
                <div class="v-guide-lbl">Votre guide</div>
                @if($showGuide && $guideUser)
                    <div class="v-guide-row">
                        @if($guideUser->avatar ?? $guideUser->profile_photo_path ?? false)
                            <img src="{{ Storage::url($guideUser->avatar ?? $guideUser->profile_photo_path) }}"
                                 alt="{{ $guideUser->name }}"
                                 class="v-guide-avatar">
                        @else
                            <div class="v-guide-avatar-ph">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--f-700)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                        @endif
                        <div>
                            <div class="v-guide-name">{{ $guideUser->first_name ?? $guideUser->name }}</div>
                            <div class="v-guide-type">{{ $guideTypeLabel }}</div>
                        </div>
                    </div>
                @else
                    <div class="v-guide-pending">
                        Votre guide vous contactera dans les 24h suivant la confirmation pour convenir du point de rendez-vous.
                    </div>
                @endif
            </div>

        </div>

    </div>{{-- fin v-body --}}

    {{-- ════ PERFORATION ════ --}}
    <div class="v-perf"><div class="v-perf-line"></div></div>

    {{-- ════ FOOTER ════ --}}
    <div class="v-footer">
        <div class="v-footer-left">
            <div>
                <div class="v-footer-brand">DiscovTrip</div>
                <div class="v-footer-sub">Bénin · Afrique de l'Ouest</div>
            </div>
            <div class="v-footer-divider"></div>
            <div class="v-footer-tagline">🇧🇯 L'Afrique Autrement</div>
        </div>
        <div class="v-footer-center">
            <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="8eede1e0faefedfaceeae7fdede1f8fafce7fea0ede1e3">[email&#160;protected]</a><br>
            +229 01 91 09 43 66<br>
            Cotonou, République du Bénin
        </div>
        <div class="v-footer-right">
            <div class="v-footer-ref-lbl">Référence</div>
            <div class="v-footer-ref-code">{{ $booking->reference }}</div>
            <div class="v-footer-url">discovtrip.com/bookings/{{ $booking->reference }}</div>
        </div>
    </div>

</div>{{-- fin .voucher --}}
</div>{{-- fin .doc-outer --}}

{{-- ── QR Code — div container (qrcodejs crée son propre élément dedans) ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    var container = document.getElementById('v-qr-container');
    if (!container || typeof QRCode === 'undefined') return;
    try {
        new QRCode(container, {
            text:         '{{ $bookingUrl }}',
            width:        72,
            height:       72,
            colorDark:    '#1C4F32',
            colorLight:   '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
        /* Force taille sur img générée */
        setTimeout(function () {
            var img = container.querySelector('img');
            if (img) { img.style.cssText = 'width:72px;height:72px;display:block;'; }
        }, 100);
    } catch (e) {
        container.innerHTML = '<svg width="72" height="72" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg"><rect width="72" height="72" fill="#e8f5ee"/><text x="36" y="40" text-anchor="middle" font-size="9" font-family="sans-serif" fill="#7a9e87">QR indisponible</text></svg>';
    }
}());
</script>

</body>
</html>