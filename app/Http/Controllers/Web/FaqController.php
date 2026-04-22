<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function show()
    {
        $faqs = [
            'reservations' => [
                'label' => 'Réservations',
                'icon'  => 'calendar-check',
                'items' => [
                    ['q' => 'Comment réserver une expérience ?',
                     'a' => 'Choisissez une expérience, sélectionnez votre date et le nombre de participants, puis suivez le processus de paiement sécurisé. Vous recevrez une confirmation par email immédiatement.'],
                    ['q' => 'Puis-je réserver pour un groupe ?',
                     'a' => 'Oui. La plupart de nos expériences acceptent des groupes. Pour les groupes de plus de 10 personnes, contactez-nous directement pour une offre sur mesure et des tarifs dégressifs.'],
                    ['q' => 'Qu\'est-ce que la réservation instantanée ?',
                     'a' => 'Les expériences avec le badge <strong>Instantané</strong> sont confirmées immédiatement après le paiement. Les autres nécessitent une validation du guide (sous 24h maximum).'],
                    ['q' => 'Puis-je modifier ma réservation après confirmation ?',
                     'a' => 'Vous pouvez modifier la date de votre réservation depuis votre espace client, sous réserve de disponibilité du guide. Toute modification doit être effectuée au moins 48h avant l\'expérience.'],
                ],
            ],
            'annulation' => [
                'label' => 'Annulation & Remboursement',
                'icon'  => 'undo',
                'items' => [
                    ['q' => 'Quelle est la politique d\'annulation ?',
                     'a' => 'Annulation <strong>gratuite jusqu\'à 48h avant</strong> l\'expérience avec remboursement intégral. Entre 24h et 48h avant : remboursement à 50%. Moins de 24h avant : aucun remboursement. Consultez notre <a href="' . route('cancellation') . '">politique complète</a>.'],
                    ['q' => 'Comment annuler depuis mon espace client ?',
                     'a' => 'Connectez-vous, accédez à "Mes réservations", trouvez la réservation concernée et cliquez sur "Annuler". Le bouton n\'apparaît que si l\'annulation gratuite est encore possible.'],
                    ['q' => 'Sous combien de temps suis-je remboursé ?',
                     'a' => 'Le remboursement est traité sous 5 à 10 jours ouvrés selon votre banque. Vous recevrez un email de confirmation dès que le remboursement est initié.'],
                    ['q' => 'Que se passe-t-il si le guide annule ?',
                     'a' => 'En cas d\'annulation par le guide, vous êtes remboursé intégralement et automatiquement dans les 48h. Nous vous proposons également une alternative si disponible.'],
                ],
            ],
            'paiement' => [
                'label' => 'Paiement',
                'icon'  => 'credit-card',
                'items' => [
                    ['q' => 'Quels modes de paiement acceptez-vous ?',
                     'a' => 'Nous acceptons les cartes bancaires (Visa, Mastercard), le FCFA en espèces pour certaines expériences, MTN Mobile Money et Moov Money. Tous les paiements en ligne sont sécurisés par chiffrement SSL 256 bits.'],
                    ['q' => 'Le paiement est-il sécurisé ?',
                     'a' => 'Absolument. Nous utilisons un protocole SSL 256 bits et ne stockons jamais vos coordonnées bancaires sur nos serveurs. Toutes les transactions sont chiffrées de bout en bout.'],
                    ['q' => 'Puis-je payer en plusieurs fois ?',
                     'a' => 'Pour les expériences de plus de 100 000 FCFA, nous proposons un paiement en deux fois (50% à la réservation, 50% 48h avant l\'expérience). Contactez-nous pour l\'activer.'],
                ],
            ],
            'experiences' => [
                'label' => 'Expériences',
                'icon'  => 'map-marked-alt',
                'items' => [
                    ['q' => 'Qui sont les guides DiscovTrip ?',
                     'a' => 'Tous nos guides sont certifiés par DiscovTrip après une sélection rigoureuse : vérification d\'identité, formation aux standards de qualité, test de leurs connaissances locales et minimum 3 expériences-tests validées.'],
                    ['q' => 'Les guides parlent-ils français et anglais ?',
                     'a' => 'Tous nos guides certifiés sont bilingues français/anglais. Certains parlent également espagnol, allemand ou fon selon les destinations. La langue est indiquée sur chaque fiche d\'expérience.'],
                    ['q' => 'Que comprend le prix affiché ?',
                     'a' => 'Le prix inclut les services du guide, les entrées des sites visités quand mentionné, et l\'assurance responsabilité civile. Les transports depuis votre hébergement sont en option sauf mention contraire.'],
                    ['q' => 'Proposez-vous des expériences privées ?',
                     'a' => 'Oui. Toutes nos expériences peuvent être privatisées pour votre groupe. Contactez-nous avec le sujet "Expérience sur mesure" pour un devis personnalisé sous 24h.'],
                ],
            ],
            'pratique' => [
                'label' => 'Infos pratiques',
                'icon'  => 'info-circle',
                'items' => [
                    ['q' => 'Faut-il créer un compte pour réserver ?',
                     'a' => 'Oui, un compte DiscovTrip est requis pour réserver. L\'inscription est gratuite et prend moins de 2 minutes. Elle vous permet aussi de gérer vos réservations et favoris.'],
                    ['q' => 'Quelle est la meilleure période pour visiter le Bénin ?',
                     'a' => 'La saison sèche (novembre à mars) est idéale pour la plupart des destinations. La saison des pluies (avril à octobre) offre une végétation luxuriante mais peut limiter l\'accès à certains sites.'],
                    ['q' => 'Comment devenir guide partenaire ?',
                     'a' => 'Envoyez-nous votre candidature via le formulaire de contact avec le sujet "Partenariat / Devenir guide". Nous examinons chaque profil et revenons vers vous sous 72h.'],
                    ['q' => 'DiscovTrip opère-t-il en dehors du Bénin ?',
                     'a' => 'Pour l\'instant, nos expériences sont exclusivement au Bénin. Une extension vers le Togo, le Nigeria et le Ghana est prévue pour 2026. Inscrivez-vous à notre newsletter pour être informé.'],
                ],
            ],
        ];

        $totalFaqs = collect($faqs)->sum(fn($cat) => count($cat['items']));

        return view('pages.faq', compact('faqs', 'totalFaqs'));
    }
}