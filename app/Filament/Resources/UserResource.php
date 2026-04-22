<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model            = User::class;
    protected static ?string $navigationLabel  = 'Clients';
    protected static ?string $modelLabel       = 'Client';
    protected static ?string $pluralModelLabel = 'Clients';
    protected static ?int    $navigationSort   = 3;

    public static function getNavigationIcon(): \BackedEnum|\Illuminate\Contracts\Support\Htmlable|string|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = User::where('role', 'client')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'primary'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations personnelles')->schema([
                TextInput::make('first_name')->label('Prénom'),
                TextInput::make('last_name')->label('Nom'),
                TextInput::make('email')->email()->unique(ignoreRecord: true),
                TextInput::make('phone')->label('Téléphone'),
            ])->columns(2),
            Section::make('Compte')->schema([
                Select::make('role')
                    ->options(['client' => 'Client', 'admin' => 'Admin', 'guide' => 'Guide'])
                    ->required()->native(false),
                Toggle::make('is_active')->label('Compte actif')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(function ($r) {
                        if (!$r) return '—';
                        $name = trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? ''));
                        return $name ?: ($r->email ?? 'Utilisateur #' . $r->id);
                    })
                    ->searchable(['first_name', 'last_name', 'email'])
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->getStateUsing(fn ($r) => $r?->email ?? '—')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->getStateUsing(fn ($r) => $r?->phone ?? '—'),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Réservations')
                    ->counts('bookings')
                    ->alignCenter()->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('wishlists_count')
                    ->label('Favoris')
                    ->counts('wishlists')
                    ->alignCenter()->badge()->color('danger'),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total dépensé')
                    ->getStateUsing(function ($r) {
                        if (!$r || !$r->id) return '0 FCFA';
                        try {
                            $total = $r->bookings()
                                ->whereIn('status', ['confirmed', 'completed'])
                                ->sum('total_price');
                            return number_format((float)$total, 0, '.', ' ') . ' FCFA';
                        } catch (\Exception $e) {
                            return '— FCFA';
                        }
                    }),

                Tables\Columns\TextColumn::make('role')->label('Rôle')->badge()
                    ->color(fn ($state) => match ($state ?? '') {
                        'admin' => 'danger', 'guide' => 'warning', default => 'primary',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')->boolean()->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')->date('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')->label('Rôle')
                    ->options(['client' => 'Clients', 'admin' => 'Admins', 'guide' => 'Guides']),
                Tables\Filters\Filter::make('active')->label('Comptes actifs')
                    ->query(fn (Builder $q) => $q->where('is_active', true)),
                Tables\Filters\Filter::make('with_bookings')->label('Ont réservé')
                    ->query(fn (Builder $q) => $q->has('bookings')),
                Tables\Filters\Filter::make('new_this_month')->label('Nouveaux ce mois')
                    ->query(fn (Builder $q) =>
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                    ),
            ])
            ->actions([
                Action::make('toggle_active')
                    ->label(fn ($r) => $r?->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn ($r) => $r?->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                    ->color(fn ($r) => $r?->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($r) {
                        if ($r) $r->update(['is_active' => !$r->is_active]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', '!=', 'admin')->whereNotNull('id');
    }
}