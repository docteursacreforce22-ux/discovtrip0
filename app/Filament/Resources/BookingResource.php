<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Offer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BookingResource extends Resource
{
    protected static ?string $model            = Booking::class;
    protected static ?string $navigationLabel  = 'Réservations';
    protected static ?string $modelLabel       = 'Réservation';
    protected static ?string $pluralModelLabel = 'Réservations';
    protected static ?int    $navigationSort   = 2;

    public static function getNavigationIcon(): \BackedEnum|\Illuminate\Contracts\Support\Htmlable|string|null
    {
        return 'heroicon-o-calendar-days';
    }

    // Badge rouge sur l'icône nav = nb de réservations en attente
    public static function getNavigationBadge(): ?string
    {
        $count = Booking::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations réservation')->schema([
                TextInput::make('reference')
                    ->label('Référence')
                    ->disabled()
                    ->copyable(),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'              => '⏳ En attente',
                        'processing'           => '🔄 En cours',
                        'confirmed'            => '✅ Confirmée',
                        'completed'            => '🏁 Terminée',
                        'cancelled_by_user'    => '❌ Annulée (client)',
                        'cancelled_by_partner' => '❌ Annulée (guide)',
                        'cancelled'            => '❌ Annulée',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('total_price')
                    ->label('Montant total (FCFA)')
                    ->disabled(),

                Toggle::make('is_paid')
                    ->label('Payé')
                    ->inline(false),
            ])->columns(2),

            Section::make('Client')->schema([
                TextInput::make('guest_first_name')->label('Prénom (invité)')->disabled(),
                TextInput::make('guest_last_name')->label('Nom (invité)')->disabled(),
                TextInput::make('guest_email')->label('Email (invité)')->disabled(),
                TextInput::make('guest_phone')->label('Téléphone (invité)')->disabled(),
            ])->columns(2)
              ->collapsible()
              ->collapsed(),

            Section::make('Notes')->schema([
                Textarea::make('notes')
                    ->label('Notes internes')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('special_requests')
                    ->label('Demandes spéciales (client)')
                    ->rows(2)
                    ->disabled()
                    ->columnSpanFull(),
            ])->collapsible()->collapsed(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('offer.title')
                    ->label('Offre')
                    ->searchable()
                    ->limit(28)
                    ->tooltip(fn ($record) => $record->offer?->title),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        $name = trim(($record->guest_first_name ?? '') . ' ' . ($record->guest_last_name ?? ''));
                        return $name ? $name . ' 👤' : '—';
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('user', fn ($q) =>
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name',  'like', "%{$search}%")
                        )
                        ->orWhere('guest_first_name', 'like', "%{$search}%")
                        ->orWhere('guest_last_name',  'like', "%{$search}%")
                        ->orWhere('guest_email',      'like', "%{$search}%");
                    }),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date expérience')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('participants')
                    ->label('Pers.')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ' ') . ' FCFA')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Payé')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'              => 'En attente',
                        'processing'           => 'En cours',
                        'confirmed'            => 'Confirmée',
                        'completed'            => 'Terminée',
                        'cancelled_by_user'    => 'Annulée (client)',
                        'cancelled_by_partner' => 'Annulée (guide)',
                        'cancelled'            => 'Annulée',
                        default                => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending'              => 'warning',
                        'processing'           => 'info',
                        'confirmed'            => 'success',
                        'completed'            => 'gray',
                        'cancelled_by_user',
                        'cancelled_by_partner',
                        'cancelled'            => 'danger',
                        default                => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Paiement')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'fedapay' => 'Mobile Money',
                        'stripe'  => 'Carte',
                        'on_site' => 'Sur place',
                        default   => $state ?? '—',
                    })
                    ->color(fn ($state) => match ($state) {
                        'fedapay' => 'warning',
                        'stripe'  => 'info',
                        'on_site' => 'gray',
                        default   => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'              => 'En attente',
                        'processing'           => 'En cours',
                        'confirmed'            => 'Confirmée',
                        'completed'            => 'Terminée',
                        'cancelled_by_user'    => 'Annulée (client)',
                        'cancelled_by_partner' => 'Annulée (guide)',
                        'cancelled'            => 'Annulée',
                    ]),

                Tables\Filters\SelectFilter::make('is_paid')
                    ->label('Paiement')
                    ->options([
                        '1' => 'Payé',
                        '0' => 'Non payé',
                    ]),

                Tables\Filters\SelectFilter::make('offer_id')
                    ->label('Offre')
                    ->options(fn () => Offer::where('status', 'published')
                        ->pluck('title', 'id')
                        ->toArray()
                    )
                    ->searchable(),

                Tables\Filters\Filter::make('this_month')
                    ->label('Ce mois')
                    ->query(fn (Builder $q) =>
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                    ),

                Tables\Filters\Filter::make('upcoming')
                    ->label('À venir (confirmées)')
                    ->query(fn (Builder $q) =>
                        $q->where('booking_date', '>=', now())
                          ->where('status', 'confirmed')
                    ),

                Tables\Filters\Filter::make('pending_payment')
                    ->label('Non payées')
                    ->query(fn (Builder $q) =>
                        $q->where('is_paid', false)
                          ->whereIn('status', ['confirmed', 'pending'])
                    ),
            ])
            ->actions([
                Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'confirmed',
                    ])),

                Action::make('mark_paid')
                    ->label('Marquer payé')
                    ->icon('heroicon-m-banknotes')
                    ->color('info')
                    ->visible(fn ($record) => ! $record->is_paid && in_array($record->status, ['confirmed', 'pending']))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'is_paid'        => true,
                        'payment_status' => 'paid',
                        'paid_at'        => now(),
                    ])),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Confirmer en masse toutes les "pending"
                    \Filament\Actions\BulkAction::make('bulk_confirm')
                        ->label('Confirmer la sélection')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) =>
                            $records->each(fn ($r) =>
                                $r->status === 'pending' && $r->update(['status' => 'confirmed'])
                            )
                        ),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBookings::route('/'),
            'view'   => Pages\ViewBooking::route('/{record}'),
            'edit'   => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}