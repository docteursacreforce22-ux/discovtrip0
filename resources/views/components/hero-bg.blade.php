{{--
    Composant : <x-hero-bg key="hero_destinations" />

    - Sans image en DB  → fond var(--f-900) + motif wax + glows (style destinations)
    - Avec image en DB  → image plein fond + overlay sombre + motif wax superposé

    Paramètres :
      key      (string) clé SiteSetting  ex: 'hero_destinations'
      fallback (string) asset fallback   ex: asset('images/hero.jpg')  [optionnel]
      id       (string) id unique SVG pattern pour éviter les conflits sur une même page
--}}
@props([
    'settingKey' => '',
    'fallback'   => '',
    'patternId'  => 'wp-hero',
])

@php
    $imgUrl = \App\Providers\AppServiceProvider::heroImage($settingKey, $fallback);
    $hasImg  = !empty($imgUrl);
@endphp

<div class="dt-hero-bg" aria-hidden="true">

    {{-- Image de fond (si disponible) --}}
    @if($hasImg)
        <img src="{{ $imgUrl }}"
             alt=""
             class="dt-hero-bg__img"
             loading="eager"
             fetchpriority="high"
             decoding="async">
        <div class="dt-hero-bg__overlay"></div>
    @endif

    {{-- Motif wax africain — toujours présent, opacité réduite si image --}}
    <svg class="dt-hero-bg__wax {{ $hasImg ? 'dt-hero-bg__wax--over-img' : '' }}"
         viewBox="0 0 800 500"
         preserveAspectRatio="xMidYMid slice"
         aria-hidden="true">
        <defs>
            <pattern id="{{ $patternId }}-d" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                <polygon points="40,4 76,40 40,76 4,40"
                         fill="none"
                         stroke="rgba(232,188,58,.12)"
                         stroke-width="1"/>
                <circle cx="40" cy="40" r="4"
                        fill="none"
                        stroke="rgba(232,188,58,.15)"
                        stroke-width="1"/>
                <line x1="40" y1="4" x2="40" y2="76" stroke="rgba(232,188,58,.06)" stroke-width=".5"/>
                <line x1="4" y1="40" x2="76" y2="40" stroke="rgba(232,188,58,.06)" stroke-width=".5"/>
            </pattern>
            <pattern id="{{ $patternId }}-t" x="0" y="0" width="56" height="56" patternUnits="userSpaceOnUse">
                <polygon points="28,4 52,48 4,48"
                         fill="none"
                         stroke="rgba(42,143,94,.10)"
                         stroke-width="1"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#{{ $patternId }}-d)"/>
        <rect width="100%" height="100%" fill="url(#{{ $patternId }}-t)" opacity=".6"/>
    </svg>

    {{-- Glows radial --}}
    <div class="dt-hero-bg__glow dt-hero-bg__glow--1"></div>
    <div class="dt-hero-bg__glow dt-hero-bg__glow--2"></div>
    <div class="dt-hero-bg__glow dt-hero-bg__glow--3"></div>

</div>