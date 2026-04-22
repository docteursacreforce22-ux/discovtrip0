<?php

use Illuminate\Support\Facades\Schedule;

// ══════════════════════════════════════════════════════════════════════════════
// routes/console.php — tâches planifiées complémentaires
//
// NOTE : Les tâches principales (reminders, sitemap, prune) sont définies
// dans App\Console\Kernel::schedule() pour bénéficier du withoutOverlapping()
// et du timezone. Ce fichier est réservé aux tâches légères ponctuelles.
// ══════════════════════════════════════════════════════════════════════════════

// ── Optimisation des caches de config/routes/vues (lundi 5h — après purge jobs)
// À n'activer qu'en production (ne pas cacher config en développement)
if (app()->isProduction()) {
    Schedule::command('optimize')->weekly()->mondays()->at('05:00');
}