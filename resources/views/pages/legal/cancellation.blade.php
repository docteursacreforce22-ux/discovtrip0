@extends('layouts.app')

@section('title', 'Politique d\'Annulation Gratuite — DiscovTrip')

@push('meta')
<meta name="description" content="Politique d'annulation DiscovTrip : annulation gratuite jusqu'à 48h avant votre expérience. Conditions de remboursement claires et transparentes.">
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
                Nos engagements
            </div>
            <h1 class="lg-hero-title">Annulation<br><em>Gratuite</em></h1>
            <div class="lg-hero-meta">
                <span><i class="fas fa-check-circle" aria-hidden="true"></i> Remboursement intégral jusqu'à 48h avant</span>
                <span><i class="fas fa-calendar" aria-hidden="true"></i> Mis à jour le 1er janvier 2025</span>
            </div>
        </div>
    </div>
</section>

<div class="lg-body">
    <div class="dt-container lg-layout">

        <nav class="lg-toc" aria-label="Table des matières">
            <div class="lg-toc-header">
                <i class="fas fa-list" aria-hidden="true"></i>
                Sommaire
            </div>
            <div class="lg-toc-list">
                @foreach([
                    '1. Résumé rapide','2. Délais & remboursements','3. Comment annuler',
                    '4. Annulation par le guide','5. Cas particuliers','6. Contact',
                ] as $i => $item)
                <a class="lg-toc-item" href="#lg-s{{ $i+1 }}" data-section="{{ $i+1 }}">
                    <span class="lg-toc-num">{{ $i+1 }}</span>
                    <span>{{ $item }}</span>
                </a>
                @endforeach
            </div>
        </nav>

        <article class="lg-content">

            {{-- Résumé visuel --}}
            <div id="lg-s1" style="margin-bottom:52px; padding-bottom:52px; border-bottom:1px solid var(--cream-3);">
                <div class="lg-section-num">Résumé</div>
                <h2 class="lg-section-title">En un <em>coup d'œil</em></h2>
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:24px;">
                    <div style="background:rgba(42,143,94,.07);border:1.5px solid rgba(42,143,94,.2);border-radius:16px;padding:22px 18px;text-align:center;">
                        <div style="font-size:32px;margin-bottom:8px;">✅</div>
                        <div style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--f-900);margin-bottom:6px;">+48h avant</div>
                        <div style="font-size:13px;color:var(--f-600);font-weight:700;">Remboursement intégral</div>
                        <div style="font-size:11px;color:#9a8a78;margin-top:4px;">100% du montant payé</div>
                    </div>
                    <div style="background:rgba(212,162,15,.07);border:1.5px solid rgba(212,162,15,.2);border-radius:16px;padding:22px 18px;text-align:center;">
                        <div style="font-size:32px;margin-bottom:8px;">⚠️</div>
                        <div style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--f-900);margin-bottom:6px;">24h – 48h</div>
                        <div style="font-size:13px;color:var(--a-600);font-weight:700;">Remboursement partiel</div>
                        <div style="font-size:11px;color:#9a8a78;margin-top:4px;">50% du montant payé</div>
                    </div>
                    <div style="background:rgba(201,57,35,.06);border:1.5px solid rgba(201,57,35,.18);border-radius:16px;padding:22px 18px;text-align:center;">
                        <div style="font-size:32px;margin-bottom:8px;">❌</div>
                        <div style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--f-900);margin-bottom:6px;">-24h avant</div>
                        <div style="font-size:13px;color:#c93923;font-weight:700;">Aucun remboursement</div>
                        <div style="font-size:11px;color:#9a8a78;margin-top:4px;">Sauf cas de force majeure</div>
                    </div>
                </div>
            </div>

            <div class="lg-section" id="lg-s2">
                <div class="lg-section-num">Article 2</div>
                <h2 class="lg-section-title">Délais & <em>remboursements</em></h2>
                <div class="lg-table-wrap">
                    <table class="lg-table">
                        <thead>
                            <tr><th>Délai d'annulation</th><th>Remboursement</th><th>Délai de versement</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Plus de 48h avant l'expérience</td>
                                <td><strong style="color:var(--f-600)">100% — Remboursement intégral</strong></td>
                                <td>5 à 10 jours ouvrés</td>
                            </tr>
                            <tr>
                                <td>Entre 24h et 48h avant l'expérience</td>
                                <td><strong style="color:var(--a-600)">50% — Remboursement partiel</strong></td>
                                <td>5 à 10 jours ouvrés</td>
                            </tr>
                            <tr>
                                <td>Moins de 24h avant l'expérience</td>
                                <td><strong style="color:#c93923">Aucun remboursement</strong></td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Non-présentation (no-show)</td>
                                <td><strong style="color:#c93923">Aucun remboursement</strong></td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Annulation par le guide</td>
                                <td><strong style="color:var(--f-600)">100% — Remboursement automatique</strong></td>
                                <td>Sous 48h</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="lg-p" style="margin-top:16px">Le remboursement est effectué sur le même moyen de paiement utilisé lors de la réservation.</p>
            </div>

            <div class="lg-section" id="lg-s3">
                <div class="lg-section-num">Article 3</div>
                <h2 class="lg-section-title">Comment <em>annuler</em></h2>
                <p class="lg-p">L'annulation se fait exclusivement depuis votre espace client DiscovTrip. Aucune annulation ne peut être effectuée par email ou téléphone.</p>
                <ul class="lg-ul">
                    <li>Connectez-vous à votre compte DiscovTrip.</li>
                    <li>Accédez à <strong>Espace client → Mes réservations</strong>.</li>
                    <li>Sélectionnez la réservation à annuler.</li>
                    <li>Cliquez sur <strong>"Annuler cette réservation"</strong> — ce bouton n'apparaît que si l'annulation gratuite est encore disponible.</li>
                    <li>Confirmez l'annulation. Un email de confirmation vous est envoyé immédiatement.</li>
                </ul>
                <div class="lg-box">
                    <div class="lg-box-title"><i class="fas fa-info-circle"></i> Bon à savoir</div>
                    <p>Le bouton d'annulation disparaît automatiquement lorsque le délai de 48h est dépassé. Si vous ne voyez plus le bouton, contactez-nous directement.</p>
                </div>
            </div>

            <div class="lg-section" id="lg-s4">
                <div class="lg-section-num">Article 4</div>
                <h2 class="lg-section-title">Annulation par le <em>guide</em></h2>
                <p class="lg-p">En cas d'annulation d'une expérience par le guide (maladie, météo exceptionnelle, force majeure), DiscovTrip s'engage à :</p>
                <ul class="lg-ul">
                    <li>Vous notifier par email et SMS dans les plus brefs délais.</li>
                    <li>Procéder au remboursement intégral <strong>automatiquement sous 48h</strong>.</li>
                    <li>Vous proposer une alternative avec un guide disponible si possible.</li>
                </ul>
                <div class="lg-box--warn lg-box">
                    <div class="lg-box-title" style="color:var(--f-600)"><i class="fas fa-star"></i> Notre engagement qualité</div>
                    <p>Les guides DiscovTrip sont soumis à une politique d'annulation stricte. Un guide qui annule de façon répétée et sans justification peut voir son statut de partenaire suspendu.</p>
                </div>
            </div>

            <div class="lg-section" id="lg-s5">
                <div class="lg-section-num">Article 5</div>
                <h2 class="lg-section-title">Cas <em>particuliers</em></h2>
                <p class="lg-p"><strong>Force majeure :</strong> En cas d'événement imprévisible et irrésistible (catastrophe naturelle, fermeture de frontières, épidémie officielle), DiscovTrip examinera chaque demande de remboursement au cas par cas, indépendamment des délais habituels.</p>
                <p class="lg-p"><strong>Maladie :</strong> Sur présentation d'un certificat médical, un remboursement partiel ou un avoir peut être accordé même hors délai, à la discrétion de DiscovTrip.</p>
                <p class="lg-p"><strong>Expériences privatisées :</strong> Les expériences réservées en privé pour des groupes peuvent avoir des conditions d'annulation différentes, précisées dans le devis personnalisé.</p>
            </div>

            <div class="lg-section" id="lg-s6">
                <div class="lg-section-num">Article 6</div>
                <h2 class="lg-section-title">Contact</h2>
                <p class="lg-p">Pour toute demande d'annulation hors délai ou cas exceptionnel, contactez notre équipe :</p>
                <div class="lg-contact">
                    <div class="lg-contact-icon"><i class="fas fa-headset" aria-hidden="true"></i></div>
                    <div>
                        <div class="lg-contact-label">Support DiscovTrip</div>
                        <div class="lg-contact-val">
                            <a href="{{ route('contact') }}">Formulaire de contact</a> ·
                            <a href="https://wa.me/22901000000">WhatsApp</a> ·
                            <a href="mailto:contact@discovtrip.com">contact@discovtrip.com</a>
                        </div>
                    </div>
                </div>
                <p class="lg-p" style="margin-top:16px">Réponse garantie sous 24h ouvrées, Lun–Ven 8h–18h, Sam 9h–14h.</p>
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