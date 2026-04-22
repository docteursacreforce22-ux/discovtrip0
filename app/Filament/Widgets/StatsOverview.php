<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Offer;
use App\Models\User;
use App\Models\Wishlist;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    // Rafraîchissement toutes les 5 minutes (300s)
    // Pas besoin de 60s — les stats ne changent pas à la seconde
    protected function getPollingInterval(): ?string
    {
        return '300s';
    }

    protected function getStats(): array
    {
        // Cache 5 minutes — évite 7 requêtes SQL à chaque rafraîchissement
        $data = Cache::remember('admin_stats_overview', 300, function () {

            $revenueThisMonth = Booking::whereIn('status', ['confirmed', 'completed'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');

            $revenueLastMonth = Booking::whereIn('status', ['confirmed', 'completed'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('total_price');

            $revenueTrend = $revenueLastMonth > 0
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                : ($revenueThisMonth > 0 ? 100 : 0);

            $bookingsThisMonth = Booking::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $bookingsPending = Booking::where('status', 'pending')->count();

            $bookingsUpcoming = Booking::where('status', 'confirmed')
                ->where('booking_date', '>=', now())
                ->count();

            $totalUsers    = User::where('role', 'client')->count();
            $newUsersMonth = User::where('role', 'client')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $activeOffers = Offer::where('status', 'published')->count();

            $wishlistCount = 0;
            try {
                $wishlistCount = Wishlist::count();
            } catch (\Exception) {}

            return compact(
                'revenueThisMonth', 'revenueLastMonth', 'revenueTrend',
                'bookingsThisMonth', 'bookingsPending', 'bookingsUpcoming',
                'totalUsers', 'newUsersMonth',
                'activeOffers', 'wishlistCount'
            );
        });

        return [
            Stat::make('Revenus ce mois', number_format((float) $data['revenueThisMonth'], 0, '.', ' ') . ' FCFA')
                ->description(($data['revenueTrend'] >= 0 ? '+' : '') . $data['revenueTrend'] . '% vs mois dernier')
                ->descriptionIcon($data['revenueTrend'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($data['revenueTrend'] >= 0 ? 'success' : 'danger'),

            Stat::make('Réservations ce mois', $data['bookingsThisMonth'])
                ->description($data['bookingsPending'] . ' en attente · ' . $data['bookingsUpcoming'] . ' confirmées à venir')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($data['bookingsPending'] > 0 ? 'warning' : 'success'),

            Stat::make('Clients inscrits', number_format($data['totalUsers'], 0, '.', ' '))
                ->description('+' . $data['newUsersMonth'] . ' nouveaux ce mois')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),

            Stat::make('Offres publiées', $data['activeOffers'])
                ->description($data['wishlistCount'] . ' fois ajoutées aux favoris')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),
        ];
    }
}