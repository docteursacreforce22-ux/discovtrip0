@extends('layouts.app')

@section('title', 'Contact — DiscovTrip')

@push('meta')
<meta name="description" content="Contactez l'équipe DiscovTrip. Nous sommes disponibles pour répondre à toutes vos questions sur nos expériences au Bénin.">
<meta property="og:title" content="Contact — DiscovTrip">
<meta property="og:description" content="Une question, un projet sur mesure ? Notre équipe locale au Bénin vous répond sous 24h.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@endpush

@push('styles')
    @vite('resources/css/pages/contact.css')
@endpush

@section('content')

{{-- ════════════════════════════════════════════
     §1  HERO — humain, contacts directs visibles
════════════════════════════════════════════ --}}
<section class="ct-hero">
    {{-- Fond : image DB ou var(--f-900) + motif wax --}}
    <x-hero-bg setting-key="hero_contact" pattern-id="wp-ct" />

    <div class="dt-container ct-hero-inner">

        {{-- Texte gauche --}}
        <div class="ct-hero-left">
            <div class="ct-hero-eyebrow">
                <span class="ct-eyebrow-dot" aria-hidden="true"></span>
                Une vraie personne vous répond
            </div>

            <h1 class="ct-hero-title">Parlons de<br>votre <em>voyage</em></h1>

            <p class="ct-hero-sub">
                Une idée, une question, un projet sur mesure —
                notre équipe locale au Bénin est là pour vous.
            </p>

            {{-- Contacts directs --}}
            <div class="ct-hero-contacts">
                <a href="tel:+22901000000" class="ct-hero-contact">
                    <span class="ct-hero-contact-icon">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                    </span>
                    <div>
                        <span class="ct-hero-contact-label">Appelez-nous</span>
                        <span class="ct-hero-contact-val">+229 01 00 00 00 00</span>
                    </div>
                </a>
                <div class="ct-hero-contact-sep" aria-hidden="true"></div>
                <a href="mailto:contact@discovtrip.com" class="ct-hero-contact">
                    <span class="ct-hero-contact-icon">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                    </span>
                    <div>
                        <span class="ct-hero-contact-label">Écrivez-nous</span>
                        <span class="ct-hero-contact-val">contact@discovtrip.com</span>
                    </div>
                </a>
            </div>

            <div class="ct-hero-hours">
                <i class="fas fa-clock" aria-hidden="true"></i>
                Lun–Ven 8h–18h · Sam 9h–14h · Réponse sous 24h
            </div>
        </div>

        {{-- Photo équipe droite --}}
        <div class="ct-hero-photo">
            @if(file_exists(public_path('images/team.jpg')))
                <img src="{{ asset('images/team.jpg') }}"
                     alt="L'équipe DiscovTrip au Bénin"
                     loading="eager">
            @else
                <div class="ct-hero-photo-ph">
                    <span>🧭</span>
                    <p>Votre équipe locale</p>
                </div>
            @endif
            <div class="ct-hero-photo-badge">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                Guides certifiés
            </div>
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════
     §2  RÉASSURANCE — 3 promesses
════════════════════════════════════════════ --}}
<div class="ct-reassurance">
    <div class="dt-container ct-reassurance-grid">
        <div class="ct-promise">
            <div class="ct-promise-icon">
                <i class="fas fa-bolt" aria-hidden="true"></i>
            </div>
            <div>
                <div class="ct-promise-title">Réponse sous 24h</div>
                <div class="ct-promise-sub">Nous lisons chaque message avec attention</div>
            </div>
        </div>
        <div class="ct-promise-div" aria-hidden="true"></div>
        <div class="ct-promise">
            <div class="ct-promise-icon">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
            </div>
            <div>
                <div class="ct-promise-title">Équipe locale</div>
                <div class="ct-promise-sub">Basée au Bénin, nous connaissons chaque recoin</div>
            </div>
        </div>
        <div class="ct-promise-div" aria-hidden="true"></div>
        <div class="ct-promise">
            <div class="ct-promise-icon">
                <i class="fas fa-sliders-h" aria-hidden="true"></i>
            </div>
            <div>
                <div class="ct-promise-title">100% personnalisé</div>
                <div class="ct-promise-sub">Chaque demande est traitée individuellement</div>
            </div>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════
     §3  FORMULAIRE + FAQ
════════════════════════════════════════════ --}}
<section class="ct-body">
    <div class="dt-container ct-grid">

        {{-- ── Formulaire (gauche) ── --}}
        <div class="ct-form-wrap">

            <div class="ct-form-head">
                <h2 class="ct-form-title">Envoyez-nous<br>un <em>message</em></h2>
                <p class="ct-form-sub">Toutes les informations transmises restent strictement confidentielles.</p>
            </div>

            @if(session('success'))
            <div class="ct-success" role="alert">
                <div class="ct-success-icon">
                    <i class="fas fa-check" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="ct-success-title">Message envoyé !</div>
                    <div class="ct-success-sub">{{ session('success') }}</div>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="ct-success" role="alert" style="background:rgba(201,57,35,.07);border-color:rgba(201,57,35,.2);">
                <div class="ct-success-icon" style="background:var(--b-500);">
                    <i class="fas fa-exclamation" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="ct-success-title" style="color:var(--b-500);">Veuillez corriger les erreurs ci-dessous</div>
                </div>
            </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST" class="ct-form" novalidate>
                @csrf

                <div class="ct-row">
                    <div class="ct-group">
                        <label for="ct-first-name">
                            Prénom <span class="ct-required" aria-hidden="true">*</span>
                        </label>
                        <input id="ct-first-name"
                               type="text" name="first_name"
                               value="{{ old('first_name') }}"
                               required autocomplete="given-name"
                               placeholder="Votre prénom"
                               class="{{ $errors->has('first_name') ? 'ct-input--error' : '' }}">
                        @error('first_name')
                            <span class="ct-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="ct-group">
                        <label for="ct-last-name">
                            Nom <span class="ct-required" aria-hidden="true">*</span>
                        </label>
                        <input id="ct-last-name"
                               type="text" name="last_name"
                               value="{{ old('last_name') }}"
                               required autocomplete="family-name"
                               placeholder="Votre nom"
                               class="{{ $errors->has('last_name') ? 'ct-input--error' : '' }}">
                        @error('last_name')
                            <span class="ct-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="ct-row">
                    <div class="ct-group">
                        <label for="ct-email">
                            Email <span class="ct-required" aria-hidden="true">*</span>
                        </label>
                        <input id="ct-email"
                               type="email" name="email"
                               value="{{ old('email') }}"
                               required autocomplete="email"
                               placeholder="votre@email.com"
                               class="{{ $errors->has('email') ? 'ct-input--error' : '' }}">
                        @error('email')
                            <span class="ct-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="ct-group">
                        <label for="ct-phone">Téléphone</label>
                        <input id="ct-phone"
                               type="tel" name="phone"
                               value="{{ old('phone') }}"
                               autocomplete="tel"
                               placeholder="+229 01 XX XX XX XX">
                    </div>
                </div>

                <div class="ct-group">
                    <label for="ct-subject">
                        Sujet <span class="ct-required" aria-hidden="true">*</span>
                    </label>
                    <div class="ct-select-wrap">
                        <select id="ct-subject" name="subject" required
                                class="{{ $errors->has('subject') ? 'ct-input--error' : '' }}">
                            <option value="">Choisir un sujet…</option>
                            <option value="info"        {{ old('subject')==='info'        ? 'selected' : '' }}>Demande d'information</option>
                            <option value="booking"     {{ old('subject')==='booking'     ? 'selected' : '' }}>Question sur une réservation</option>
                            <option value="custom"      {{ old('subject')==='custom'      ? 'selected' : '' }}>Expérience sur mesure</option>
                            <option value="partnership" {{ old('subject')==='partnership' ? 'selected' : '' }}>Partenariat / Devenir guide</option>
                            <option value="press"       {{ old('subject')==='press'       ? 'selected' : '' }}>Presse / Médias</option>
                            <option value="other"       {{ old('subject')==='other'       ? 'selected' : '' }}>Autre</option>
                        </select>
                        <i class="fas fa-chevron-down ct-select-arrow" aria-hidden="true"></i>
                    </div>
                    @error('subject')
                        <span class="ct-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ct-group">
                    <label for="ct-message">
                        Message <span class="ct-required" aria-hidden="true">*</span>
                        <span class="ct-char-count" id="ct-char-count" aria-live="polite">0 / 2000</span>
                    </label>
                    <textarea id="ct-message"
                              name="message" required rows="6"
                              placeholder="Décrivez votre demande en détail…"
                              maxlength="2000"
                              class="{{ $errors->has('message') ? 'ct-input--error' : '' }}">{{ old('message') }}</textarea>
                    @error('message')
                        <span class="ct-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="ct-submit">
                    <span>Envoyer le message</span>
                    <span class="ct-submit-arrow" aria-hidden="true">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </button>

                <p class="ct-gdpr">
                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                    Vos données sont protégées conformément au RGPD et ne seront jamais partagées.
                </p>

            </form>
        </div>

        {{-- ── FAQ (droite) ── --}}
        <aside class="ct-faq-wrap">

            <div class="ct-faq-header">
                <div class="ct-section-label">
                    <span class="ct-label-bar"></span>
                    <span>FAQ</span>
                </div>
                <h2 class="ct-faq-title">Questions <em>fréquentes</em></h2>
            </div>

            <div class="ct-faq-list">
                @foreach([
                    [
                        'q' => 'Comment annuler une réservation ?',
                        'r' => "Vous pouvez annuler gratuitement jusqu'à 48h avant l'expérience depuis votre espace client. Au-delà, contactez-nous directement.",
                    ],
                    [
                        'q' => 'Quels modes de paiement acceptez-vous ?',
                        'r' => 'Nous acceptons le FCFA, l\'Euro et les paiements Mobile Money (MTN Money, Moov Money). Le paiement en ligne est sécurisé.',
                    ],
                    [
                        'q' => 'Proposez-vous des expériences privées ?',
                        'r' => 'Absolument. Groupes familiaux, incentives d\'entreprise, voyages de noces — contactez-nous pour une offre entièrement personnalisée.',
                    ],
                    [
                        'q' => 'Les guides parlent-ils français et anglais ?',
                        'r' => 'Tous nos guides certifiés sont bilingues. Certains parlent également espagnol ou fon selon les destinations.',
                    ],
                    [
                        'q' => 'Comment devenir guide partenaire ?',
                        'r' => 'Envoyez-nous un message avec le sujet "Partenariat / Devenir guide". Nous étudions chaque profil avec attention.',
                    ],
                ] as $i => $faq)
                <details class="ct-faq-item" {{ $i === 0 ? 'open' : '' }}>
                    <summary class="ct-faq-q">
                        <span>{{ $faq['q'] }}</span>
                        <span class="ct-faq-icon" aria-hidden="true">
                            <i class="fas fa-plus  ct-faq-plus"></i>
                            <i class="fas fa-minus ct-faq-minus"></i>
                        </span>
                    </summary>
                    <p class="ct-faq-r">{{ $faq['r'] }}</p>
                </details>
                @endforeach
            </div>

            {{-- Encart WhatsApp --}}
            <a href="https://wa.me/22901000000"
               target="_blank" rel="noopener noreferrer"
               class="ct-whatsapp">
                <div class="ct-whatsapp-icon">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                </div>
                <div class="ct-whatsapp-body">
                    <div class="ct-whatsapp-title">Discuter sur WhatsApp</div>
                    <div class="ct-whatsapp-sub">Réponse rapide en journée</div>
                </div>
                <i class="fas fa-arrow-right ct-whatsapp-arrow" aria-hidden="true"></i>
            </a>

            {{-- Coordonnées discrètes --}}
            <div class="ct-contact-list">
                <a href="tel:+22901000000" class="ct-contact-item">
                    <span class="ct-contact-icon"><i class="fas fa-phone" aria-hidden="true"></i></span>
                    <div>
                        <div class="ct-contact-label">Téléphone</div>
                        <div class="ct-contact-val">+229 01 00 00 00 00</div>
                    </div>
                </a>
                <a href="mailto:contact@discovtrip.com" class="ct-contact-item">
                    <span class="ct-contact-icon"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                    <div>
                        <div class="ct-contact-label">Email</div>
                        <div class="ct-contact-val">contact@discovtrip.com</div>
                    </div>
                </a>
                <div class="ct-contact-item">
                    <span class="ct-contact-icon"><i class="fas fa-clock" aria-hidden="true"></i></span>
                    <div>
                        <div class="ct-contact-label">Horaires</div>
                        <div class="ct-contact-val">Lun–Ven 8h–18h · Sam 9h–14h</div>
                    </div>
                </div>
                <div class="ct-contact-item">
                    <span class="ct-contact-icon"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
                    <div>
                        <div class="ct-contact-label">Adresse</div>
                        <div class="ct-contact-val">Cotonou, Bénin — Haie Vive</div>
                    </div>
                </div>
            </div>

        </aside>

    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {

    /* ── Compteur textarea ── */
    const textarea = document.getElementById('ct-message');
    const counter  = document.getElementById('ct-char-count');
    if (textarea && counter) {
        const update = () => {
            const n = textarea.value.length;
            counter.textContent = n + ' / 2000';
            counter.style.color = n > 1800 ? 'var(--b-500)' : '';
        };
        textarea.addEventListener('input', update);
        update();
    }

    /* ── FAQ accordion : un seul ouvert à la fois ── */
    document.querySelectorAll('.ct-faq-item').forEach(item => {
        item.addEventListener('toggle', () => {
            if (item.open) {
                document.querySelectorAll('.ct-faq-item[open]').forEach(other => {
                    if (other !== item) other.removeAttribute('open');
                });
            }
        });
    });

})();
</script>
@endpush