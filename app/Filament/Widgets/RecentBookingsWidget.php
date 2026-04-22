<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières réservations';

    protected function getPollingInterval(): ?string
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['offer.city', 'user'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->weight('bold')
                    ->color('primary')
                    ->fontFamily('mono')
                    ->copyable(),

                Tables\Columns\TextColumn::make('offer.title')
                    ->label('Expérience')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->offer?->title),

                Tables\Columns\TextColumn::make('client')
                    ->label('Client')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        $name = trim(($record->guest_first_name ?? '') . ' ' . ($record->guest_last_name ?? ''));
                        return $name ?: '—';
                    }),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ' ') . ' FCFA')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'              => 'En attente',
                        'confirmed'            => 'Confirmée',
                        'completed'            => 'Terminée',
                        'cancelled_by_user',
                        'cancelled_by_partner',
                        'cancelled'            => 'Annulée',
                        default                => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'gray',
                        default     => 'danger',
                    }),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Payé')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                // CORRECTION : ViewAction au lieu de Action (Filament v5)
                \Filament\Actions\Action::make('view')
                    ->url(fn ($record) => route('filament.admin.resources.bookings.view', $record)),
            ])
            ->paginated(false);
    }
}