<?php

namespace App\Filament\Resources\OfferResource\RelationManagers;

use App\Models\OfferTier;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TiersRelationManager extends RelationManager
{
    protected static string $relationship = 'tiers';
    protected static ?string $title = 'Niveaux de l\'offre';
    protected static ?string $label = 'Niveau';
    protected static ?string $pluralLabel = 'Niveaux';

    public function form(Schema $form): Schema
    {
        return $form->components([

            Section::make('Identité du niveau')
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type de niveau')
                        ->options(OfferTier::TYPES)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $set('label', OfferTier::TYPES[$state] ?? '');
                            $set('tagline', OfferTier::TAGLINES[$state] ?? '');
                            if ($state === 'exception') {
                                $set('whatsapp_only', true);
                                $set('price_is_indicative', true);
                            }
                        }),

                    Forms\Components\TextInput::make('label')
                        ->label('Nom affiché')
                        ->required()
                        ->maxLength(60)
                        ->placeholder('ex: Découverte'),

                    Forms\Components\TextInput::make('tagline')
                        ->label('Accroche courte')
                        ->maxLength(120)
                        ->placeholder('ex: L\'essentiel de l\'expérience')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Tarif')
                ->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Prix (FCFA)')
                        ->numeric()
                        ->required()
                        ->prefix('FCFA')
                        ->minValue(0),

                    Forms\Components\Select::make('currency')
                        ->label('Devise')
                        ->options([
                            'XOF' => 'FCFA (XOF)',
                            'EUR' => 'Euro (EUR)',
                            'USD' => 'Dollar (USD)',
                        ])
                        ->default('XOF')
                        ->required(),

                    Forms\Components\Toggle::make('price_is_indicative')
                        ->label('Prix indicatif (affiche "à partir de")')
                        ->helperText('Recommandé pour le niveau Exception')
                        ->default(false),

                    Forms\Components\Toggle::make('whatsapp_only')
                        ->label('Finalisation via WhatsApp uniquement')
                        ->helperText('Remplace le bouton de paiement direct par WhatsApp')
                        ->default(false),
                ])
                ->columns(2),

            Section::make('Contenu')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Description du niveau')
                        ->rows(3)
                        ->placeholder('Décrivez ce que ce niveau apporte en plus...')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('included_items')
                        ->label('Ce qui est inclus dans ce niveau')
                        ->schema([
                            Forms\Components\TextInput::make('item')
                                ->label('Élément inclus')
                                ->required()
                                ->placeholder('ex: Transport climatisé A/R'),
                        ])
                        ->addActionLabel('+ Ajouter un élément')
                        ->defaultItems(0)
                        ->reorderable()
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('excluded_items')
                        ->label('Non inclus dans ce niveau')
                        ->schema([
                            Forms\Components\TextInput::make('item')
                                ->label('Élément non inclus')
                                ->required()
                                ->placeholder('ex: Repas du soir'),
                        ])
                        ->addActionLabel('+ Ajouter un élément')
                        ->defaultItems(0)
                        ->reorderable()
                        ->columnSpanFull(),
                ]),

            Section::make('Paramètres')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Niveau actif')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0)
                        ->helperText('0 = Découverte · 1 = Confort · 2 = Exception'),
                ])
                ->columns(2),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'discovery' => 'gray',
                        'comfort'   => 'warning',
                        'exception' => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => OfferTier::TYPES[$state] ?? $state),

                Tables\Columns\TextColumn::make('label')
                    ->label('Nom')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->price_is_indicative ? '≥ ' : '') .
                        number_format($state, 0, '', ' ') . ' FCFA'
                    ),

                Tables\Columns\IconColumn::make('whatsapp_only')
                    ->label('WhatsApp only')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter un niveau'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Aucun niveau configuré')
            ->emptyStateDescription('Ajoutez au moins un niveau Découverte pour cette offre.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
}