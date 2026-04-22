<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Commandes custom enregistrées explicitement.
     * Note : $this->load(__DIR__.'/Commands') les auto-découvre aussi,
     * mais cette liste sert de documentation vivante.
     */
    protected $commands = [
        \App\Console\Commands\GenerateSitemap::class,
        \App\Console\Commands\SendBookingReminders::class,
    ];

    /**
     * Planification des tâches — fuseau Africa/Porto-Novo appliqué globalement.
     */
    protected function schedule(Schedule $schedule): void
    {
        $tz = 'Africa/Porto-Novo';

        // ── Réservations en attente expirées (toutes les 15 min)
        $schedule->job(new \App\Jobs\ExpirePendingBookings)
                 ->everyFifteenMinutes()
                 ->timezone($tz)
                 ->withoutOverlapping()
                 ->runInBackground();

        // ── Rappels J-1 aux voyageurs (8h00 heure locale)
        // SendDailyReminders et bookings:send-reminders font la même chose —
        // on garde le Job (async) et on supprime la commande redondante dans console.php
        $schedule->job(new \App\Jobs\SendDailyReminders)
                 ->dailyAt('08:00')
                 ->timezone($tz)
                 ->withoutOverlapping();

        // ── Regénération du sitemap (2h du matin, chaque nuit)
        $schedule->command('sitemap:generate')
                 ->dailyAt('02:00')
                 ->timezone($tz)
                 ->runInBackground();

        // ── Nettoyage des modèles soft-deleted (dimanche 3h)
        $schedule->command('model:prune')
                 ->weekly()
                 ->sundays()
                 ->at('03:00')
                 ->timezone($tz);

        // ── Purge des jobs échoués de plus de 7 jours (lundi 4h)
        $schedule->command('queue:prune-failed --hours=168')
                 ->weekly()
                 ->mondays()
                 ->at('04:00')
                 ->timezone($tz);

        // ── Vider le cache des stats et vues compilées (dimanche 3h30)
        $schedule->command('cache:clear')
                 ->weekly()
                 ->sundays()
                 ->at('03:30')
                 ->timezone($tz);
    }

    /**
     * Enregistrement des commandes — auto-découverte + fichier console.php
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}