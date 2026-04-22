@extends('layouts.app')

@section('title', 'Politique de Confidentialité — DiscovTrip')

@push('meta')
<meta name="description" content="Politique de confidentialité et protection des données personnelles de DiscovTrip. Conformité RGPD.">
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
            <h1 class="lg-hero-title">Politique de<br><em>Confidentialité</em></h1>
            <div class="lg-hero-meta">
                <span><i class="fas fa-calendar" aria-hidden="true"></i> Dernière mise à jour : 1er janvier 2025</span>
                <span><i class="fas fa-shield-alt" aria-hidden="true"></i> Conforme RGPD</span>
                <span><i class="fas fa-lock" aria-hidden="true"></i> Chiffrement SSL 256 bits</span>
            </div>
        </div>
    </div>
</section>

<div class="lg-body">
    <div class="dt-container lg-layout">

        <nav class="lg-toc" aria-label="Table des matières">
            <div class="lg-toc-header">
                <i class="fas fa-list" aria-hidden="true"></i>
                Table des matières
            </div>
            <div class="lg-toc-list">
                @foreach([
                    '1. Responsable du traitement','2. Données collectées',
                    '3. Finalités','4. Base légale','5. Durée de conservation',
                    '6. Partage des données','7. Vos droits','8. Cookies','9. Contact DPO',
                ] as $i => $item)
                <a class="lg-toc-item" href="#lg-s{{ $i+1 }}" data-section="{{ $i+1 }}">
                    <span class="lg-toc-num">{{ $i+1 }}</span>
                    <span>{{ $item }}</span>
                </a>
                @endforeach
            </div>
        </nav>

        <article class="lg-content">

            <div class="lg-box">
                <div class="lg-box-title"><i class="fas fa-shield-alt"></i> Engagement de confidentialité</div>
                <p>Vos données personnelles ne sont jamais vendues à des tiers. DiscovTrip s'engage à les protéger conformément au RGPD européen et à la législation béninoise.</p>
            </div>

            <div class="lg-section" id="lg-s1">
                <div class="lg-section-num">Article 1</div>
                <h2 class="lg-section-title">Responsable du traitement</h2>
                <p class="lg-p">Le responsable du traitement de vos données personnelles est :</p>
                <div class="lg-contact">
                    <div class="lg-contact-icon"><i class="fas fa-building" aria-hidden="true"></i></div>
                    <div>
                        <div class="lg-contact-label">Identité</div>
                        <div class="lg-contact-val">DiscovTrip SARL · Cotonou, Bénin — Haie Vive<br>
                        <a href="mailto:privacy@discovtrip.com">privacy@discovtrip.com</a></div>
                    </div>
                </div>
            </div>

            <div class="lg-section" id="lg-s2">
                <div class="lg-section-num">Article 2</div>
                <h2 class="lg-section-title">Données collectées</h2>
                <p class="lg-p">Nous collectons uniquement les données nécessaires à la fourniture de nos services :</p>
                <div class="lg-table-wrap">
                    <table class="lg-table">
                        <thead>
                            <tr><th>Catégorie</th><th>Données</th><th>Obligatoire</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Identité</td><td>Prénom, nom</td><td>✅ Oui</td></tr>
                            <tr><td>Contact</td><td>Adresse email, téléphone</td><td>✅ Email oui / Téléphone non</td></tr>
                            <tr><td>Réservations</td><td>Historique, préférences</td><td>✅ Oui</td></tr>
                            <tr><td>Paiement</td><td>Référence transaction (jamais les données brutes)</td><td>✅ Oui</td></tr>
                            <tr><td>Navigation</td><td>Adresse IP, cookies techniques</td><td>Partiel</td></tr>
                            <tr><td>Photo de profil</td><td>Image uploadée volontairement</td><td>❌ Non</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg-section" id="lg-s3">
                <div class="lg-section-num">Article 3</div>
                <h2 class="lg-section-title">Finalités du traitement</h2>
                <ul class="lg-ul">
                    <li>Gestion de votre compte et authentification sécurisée.</li>
                    <li>Traitement et confirmation de vos réservations.</li>
                    <li>Envoi d'emails transactionnels (confirmation, rappels, annulations).</li>
                    <li>Amélioration de nos services et personnalisation de l'expérience.</li>
                    <li>Conformité légale et prévention de la fraude.</li>
                    <li>Newsletter et offres promotionnelles <strong>uniquement avec votre consentement explicite.</strong></li>
                </ul>
            </div>

            <div class="lg-section" id="lg-s4">
                <div class="lg-section-num">Article 4</div>
                <h2 class="lg-section-title">Base légale du traitement</h2>
                <ul class="lg-ul">
                    <li><strong>Exécution du contrat :</strong> traitement des réservations et des paiements.</li>
                    <li><strong>Intérêt légitime :</strong> sécurité du service, prévention des abus.</li>
                    <li><strong>Consentement :</strong> emails marketing, cookies non essentiels.</li>
                    <li><strong>Obligation légale :</strong> conservation des données comptables.</li>
                </ul>
            </div>

            <div class="lg-section" id="lg-s5">
                <div class="lg-section-num">Article 5</div>
                <h2 class="lg-section-title">Durée de conservation</h2>
                <div class="lg-table-wrap">
                    <table class="lg-table">
                        <thead>
                            <tr><th>Type de données</th><th>Durée de conservation</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Données de compte actif</td><td>Pendant la durée du compte + 3 ans</td></tr>
                            <tr><td>Données de réservation</td><td>5 ans (obligation comptable)</td></tr>
                            <tr><td>Données marketing</td><td>3 ans après dernier contact</td></tr>
                            <tr><td>Logs de sécurité</td><td>12 mois</td></tr>
                            <tr><td>Compte supprimé (anonymisation)</td><td>Immédiate — données anonymisées conservées à des fins statistiques</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg-section" id="lg-s6">
                <div class="lg-section-num">Article 6</div>
                <h2 class="lg-section-title">Partage des données</h2>
                <p class="lg-p"><strong>Vos données ne sont jamais vendues.</strong> Elles peuvent être partagées uniquement avec :</p>
                <ul class="lg-ul">
                    <li><strong>Le guide concerné :</strong> prénom, email et téléphone pour organiser votre expérience.</li>
                    <li><strong>Prestataires de paiement :</strong> pour le traitement sécurisé des transactions.</li>
                    <li><strong>Hébergeur :</strong> infrastructure technique (données chiffrées).</li>
                    <li><strong>Autorités compétentes :</strong> uniquement sur réquisition légale.</li>
                </ul>
            </div>

            <div class="lg-section" id="lg-s7">
                <div class="lg-section-num">Article 7</div>
                <h2 class="lg-section-title">Vos droits</h2>
                <p class="lg-p">Conformément au RGPD, vous disposez des droits suivants sur vos données personnelles :</p>
                <ul class="lg-ul">
                    <li><strong>Droit d'accès :</strong> obtenir une copie de vos données.</li>
                    <li><strong>Droit de rectification :</strong> corriger des données inexactes.</li>
                    <li><strong>Droit à l'effacement :</strong> supprimer votre compte depuis votre espace client (anonymisation immédiate).</li>
                    <li><strong>Droit à la portabilité :</strong> recevoir vos données dans un format structuré.</li>
                    <li><strong>Droit d'opposition :</strong> vous opposer au traitement à des fins marketing.</li>
                    <li><strong>Droit de limitation :</strong> limiter certains traitements en cas de contestation.</li>
                </ul>
                <div class="lg-box">
                    <div class="lg-box-title"><i class="fas fa-user-shield"></i> Exercer vos droits</div>
                    <p>Depuis votre espace client → <strong>Sécurité</strong>, ou par email à <a href="mailto:privacy@discovtrip.com" style="color:var(--a-600);font-weight:600;">privacy@discovtrip.com</a>. Réponse sous 30 jours.</p>
                </div>
            </div>

            <div class="lg-section" id="lg-s8">
                <div class="lg-section-num">Article 8</div>
                <h2 class="lg-section-title">Cookies</h2>
                <p class="lg-p">Nous utilisons des cookies techniques (strictement nécessaires au fonctionnement) et des cookies analytiques (avec votre consentement).</p>
                <div class="lg-table-wrap">
                    <table class="lg-table">
                        <thead>
                            <tr><th>Cookie</th><th>Type</th><th>Durée</th><th>Finalité</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>laravel_session</td><td>Technique</td><td>Session</td><td>Authentification</td></tr>
                            <tr><td>XSRF-TOKEN</td><td>Technique</td><td>Session</td><td>Sécurité (protection CSRF)</td></tr>
                            <tr><td>remember_web_*</td><td>Technique</td><td>30 jours</td><td>"Se souvenir de moi"</td></tr>
                            <tr><td>locale</td><td>Technique</td><td>Session</td><td>Langue préférée</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="lg-p">Aucun cookie publicitaire n'est utilisé sur DiscovTrip.</p>
            </div>

            <div class="lg-section" id="lg-s9">
                <div class="lg-section-num">Article 9</div>
                <h2 class="lg-section-title">Contact & Délégué à la Protection des Données</h2>
                <p class="lg-p">Pour toute question relative à la protection de vos données ou pour exercer vos droits :</p>
                <div class="lg-contact">
                    <div class="lg-contact-icon"><i class="fas fa-user-shield" aria-hidden="true"></i></div>
                    <div>
                        <div class="lg-contact-label">DPO — DiscovTrip</div>
                        <div class="lg-contact-val">
                            <a href="mailto:privacy@discovtrip.com">privacy@discovtrip.com</a> ·
                            <a href="{{ route('contact') }}">Formulaire de contact</a>
                        </div>
                    </div>
                </div>
            </div>

        </article>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
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
    tocItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('lg-s' + item.dataset.section)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>
@endpush