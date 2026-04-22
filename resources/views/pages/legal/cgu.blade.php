@extends('layouts.app')

@section('title', 'Conditions Générales d\'Utilisation — DiscovTrip')

@push('meta')
<meta name="description" content="Conditions générales d'utilisation de DiscovTrip. Droits et obligations des utilisateurs de la plateforme d'expériences au Bénin.">
<meta name="robots" content="noindex, follow">
@endpush

@push('styles')
    @vite('resources/css/pages/legal.css')
@endpush

@section('content')

<section class="lg-hero">
    <div class="lg-hero-glow" aria-hidden="true"></div>
    <div class="dt-container">
        <div class="lg-hero-inner">
            <div class="lg-hero-eyebrow">
                <span aria-hidden="true"></span>
                Documents légaux
            </div>
            <h1 class="lg-hero-title">Conditions Générales<br><em>d'Utilisation</em></h1>
            <div class="lg-hero-meta">
                <span><i class="fas fa-calendar" aria-hidden="true"></i> Dernière mise à jour : 1er janvier 2025</span>
                <span><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Applicable au Bénin</span>
                <span><i class="fas fa-language" aria-hidden="true"></i> Version française</span>
            </div>
        </div>
    </div>
</section>

<div class="lg-body">
    <div class="dt-container lg-layout">

        {{-- ── Table des matières ── --}}
        <nav class="lg-toc" aria-label="Table des matières">
            <div class="lg-toc-header">
                <i class="fas fa-list" aria-hidden="true"></i>
                Table des matières
            </div>
            <div class="lg-toc-list" id="lg-toc-list">
                @foreach([
                    '1. Objet','2. Définitions','3. Acceptation','4. Inscription',
                    '5. Réservations','6. Paiements','7. Responsabilités',
                    '8. Propriété intellectuelle','9. Protection des données','10. Contact',
                ] as $i => $item)
                <a class="lg-toc-item" href="#lg-s{{ $i+1 }}" data-section="{{ $i+1 }}">
                    <span class="lg-toc-num">{{ $i+1 }}</span>
                    <span>{{ $item }}</span>
                </a>
                @endforeach
            </div>
        </nav>

        {{-- ── Contenu ── --}}
        <article class="lg-content">

            <div class="lg-box">
                <div class="lg-box-title"><i class="fas fa-info-circle"></i> Résumé</div>
                <p>Ces CGU régissent l'utilisation de la plateforme DiscovTrip. En vous inscrivant, vous acceptez ces conditions. Si vous avez des questions, <a href="{{ route('contact') }}" style="color:var(--a-600);font-weight:600;">contactez-nous</a>.</p>
            </div>

            <div class="lg-section" id="lg-s1">
                <div class="lg-section-num">Article 1</div>
                <h2 class="lg-section-title">Objet</h2>
                <p class="lg-p">Les présentes Conditions Générales d'Utilisation (ci-après "CGU") ont pour objet de définir les modalités et conditions d'utilisation des services proposés par <strong>DiscovTrip</strong>, plateforme de mise en relation entre voyageurs et guides locaux certifiés au Bénin.</p>
                <p class="lg-p">DiscovTrip est exploitée par la société DiscovTrip SARL, immatriculée au registre du commerce de Cotonou, Bénin.</p>
            </div>

            <div class="lg-section" id="lg-s2">
                <div class="lg-section-num">Article 2</div>
                <h2 class="lg-section-title">Définitions</h2>
                <ul class="lg-ul">
                    <li><strong>Plateforme :</strong> Le site web discovtrip.com et ses applications associées.</li>
                    <li><strong>Utilisateur :</strong> Toute personne physique ou morale inscrite sur la Plateforme.</li>
                    <li><strong>Voyageur :</strong> Utilisateur qui réserve une ou plusieurs Expériences.</li>
                    <li><strong>Guide :</strong> Prestataire local certifié par DiscovTrip proposant des Expériences.</li>
                    <li><strong>Expérience :</strong> Activité touristique, culturelle ou gastronomique proposée par un Guide.</li>
                    <li><strong>Réservation :</strong> Contrat conclu entre un Voyageur et un Guide via la Plateforme.</li>
                </ul>
            </div>

            <div class="lg-section" id="lg-s3">
                <div class="lg-section-num">Article 3</div>
                <h2 class="lg-section-title">Acceptation des conditions</h2>
                <p class="lg-p">L'utilisation de la Plateforme implique l'acceptation pleine et entière des présentes CGU. Toute personne qui ne souhaite pas être liée par ces CGU doit s'abstenir d'utiliser la Plateforme.</p>
                <p class="lg-p">DiscovTrip se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés par email de tout changement substantiel. L'utilisation continue de la Plateforme après notification vaut acceptation des nouvelles CGU.</p>
            </div>

            <div class="lg-section" id="lg-s4">
                <div class="lg-section-num">Article 4</div>
                <h2 class="lg-section-title">Inscription et compte utilisateur</h2>
                <p class="lg-p">L'inscription est gratuite et ouverte à toute personne physique majeure (18 ans et plus). L'utilisateur s'engage à fournir des informations exactes, complètes et à les maintenir à jour.</p>
                <ul class="lg-ul">
                    <li>Un seul compte par personne est autorisé.</li>
                    <li>Les identifiants sont personnels et confidentiels.</li>
                    <li>L'utilisateur est responsable de toute activité effectuée depuis son compte.</li>
                    <li>En cas de compromission suspectée, l'utilisateur doit en informer immédiatement DiscovTrip.</li>
                </ul>
                <div class="lg-box">
                    <div class="lg-box-title"><i class="fas fa-shield-alt"></i> Sécurité du compte</div>
                    <p>DiscovTrip ne vous demandera jamais votre mot de passe par email ou téléphone. En cas de doute, contactez-nous directement.</p>
                </div>
            </div>

            <div class="lg-section" id="lg-s5">
                <div class="lg-section-num">Article 5</div>
                <h2 class="lg-section-title">Réservations</h2>
                <p class="lg-p">Toute réservation constitue un contrat ferme entre le Voyageur et le Guide. DiscovTrip agit en qualité d'intermédiaire technique et financier.</p>
                <p class="lg-p">La réservation est confirmée dès réception du paiement intégral. Un email de confirmation est envoyé au Voyageur avec tous les détails de l'Expérience.</p>
                <div class="lg-box--warn lg-box">
                    <div class="lg-box-title" style="color:var(--f-600)"><i class="fas fa-calendar-times"></i> Annulation</div>
                    <p>La politique d'annulation s'applique à chaque réservation. Consultez notre <a href="{{ route('cancellation') }}" style="color:var(--f-600);font-weight:600;">politique d'annulation complète</a> avant de réserver.</p>
                </div>
            </div>

            <div class="lg-section" id="lg-s6">
                <div class="lg-section-num">Article 6</div>
                <h2 class="lg-section-title">Paiements et tarifs</h2>
                <p class="lg-p">Tous les prix sont affichés en Franc CFA (FCFA) et sont TTC. DiscovTrip se réserve le droit de modifier les tarifs à tout moment, sans affecter les réservations déjà confirmées.</p>
                <div class="lg-table-wrap">
                    <table class="lg-table">
                        <thead>
                            <tr><th>Mode de paiement</th><th>Devise</th><th>Frais</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Carte bancaire (Visa / Mastercard)</td><td>FCFA / EUR</td><td>Aucun frais supplémentaire</td></tr>
                            <tr><td>MTN Mobile Money</td><td>FCFA</td><td>Aucun frais supplémentaire</td></tr>
                            <tr><td>Moov Money</td><td>FCFA</td><td>Aucun frais supplémentaire</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg-section" id="lg-s7">
                <div class="lg-section-num">Article 7</div>
                <h2 class="lg-section-title">Responsabilités</h2>
                <p class="lg-p">DiscovTrip s'engage à mettre en œuvre tous les moyens raisonnables pour assurer la disponibilité et le bon fonctionnement de la Plateforme, mais ne peut être tenu responsable des interruptions techniques indépendantes de sa volonté.</p>
                <p class="lg-p">DiscovTrip ne saurait être tenu responsable des dommages directs ou indirects résultant de l'utilisation ou de l'impossibilité d'utiliser la Plateforme, dans les limites permises par la loi béninoise.</p>
                <ul class="lg-ul">
                    <li>L'utilisateur est responsable de l'exactitude des informations fournies.</li>
                    <li>Le Guide est responsable de la qualité et de la conformité de son Expérience.</li>
                    <li>DiscovTrip certifie les Guides mais ne peut garantir l'absence de tout incident.</li>
                </ul>
            </div>

            <div class="lg-section" id="lg-s8">
                <div class="lg-section-num">Article 8</div>
                <h2 class="lg-section-title">Propriété intellectuelle</h2>
                <p class="lg-p">L'ensemble des éléments constituant la Plateforme (textes, images, logos, icônes, sons, logiciels) sont la propriété exclusive de DiscovTrip ou de ses partenaires et sont protégés par les lois relatives à la propriété intellectuelle.</p>
                <p class="lg-p">Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie des éléments de la Plateforme est interdite sans autorisation écrite préalable de DiscovTrip.</p>
            </div>

            <div class="lg-section" id="lg-s9">
                <div class="lg-section-num">Article 9</div>
                <h2 class="lg-section-title">Protection des données personnelles</h2>
                <p class="lg-p">DiscovTrip traite vos données personnelles conformément au RGPD et à la loi béninoise sur la protection des données. Pour plus d'informations, consultez notre <a href="{{ route('privacy') }}" style="color:var(--a-600);font-weight:600;">Politique de Confidentialité</a>.</p>
            </div>

            <div class="lg-section" id="lg-s10">
                <div class="lg-section-num">Article 10</div>
                <h2 class="lg-section-title">Contact et litiges</h2>
                <p class="lg-p">Pour toute question relative aux présentes CGU, ou en cas de litige, nous vous invitons à nous contacter en priorité pour une résolution amiable.</p>
                <div class="lg-contact">
                    <div class="lg-contact-icon"><i class="fas fa-envelope" aria-hidden="true"></i></div>
                    <div>
                        <div class="lg-contact-label">Contact légal</div>
                        <div class="lg-contact-val"><a href="mailto:legal@discovtrip.com">legal@discovtrip.com</a> · <a href="{{ route('contact') }}">Formulaire de contact</a></div>
                    </div>
                </div>
                <p class="lg-p" style="margin-top:16px">En cas de litige persistant, les tribunaux compétents de Cotonou (Bénin) seront seuls compétents, sauf disposition légale contraire.</p>
            </div>

        </article>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    /* Highlight TOC item actif au scroll */
    const sections = document.querySelectorAll('[id^="lg-s"]');
    const tocItems = document.querySelectorAll('.lg-toc-item[data-section]');
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const id = e.target.id.replace('lg-s','');
                tocItems.forEach(t => t.classList.toggle('lg-toc-active', t.dataset.section === id));
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });
    sections.forEach(s => obs.observe(s));

    /* Smooth scroll */
    tocItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const target = document.getElementById('lg-s' + item.dataset.section);
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>
@endpush