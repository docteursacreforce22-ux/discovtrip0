<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpotlightResource\Pages;
use App\Models\Spotlight;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

class SpotlightResource extends Resource
{
    protected static ?string $model = Spotlight::class;
    protected static ?string $modelLabel = 'Spotlight';
    protected static ?string $pluralModelLabel = 'Spotlights';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-light-bulb';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contenu';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenu principal')
                    ->schema([
                        TextInput::make('badge_text')
                            ->label('Texte du badge (ex: Patrimoine Béninois)')
                            ->required()
                            ->default('Patrimoine Béninois'),

                        TextInput::make('badge_icon')
                            ->label('Icône Font Awesome (ex: fa-crown)')
                            ->default('fa-crown')
                            ->helperText('Voir fontawesome.com pour les icônes'),

                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Ex: Les Guerrières Amazones du Dahomey'),

                        TextInput::make('highlight_word')
                            ->label('Mot mis en évidence (en cuivre)')
                            ->helperText('Ce mot/groupe dans le titre sera mis en couleur. Ex: Amazones du Dahomey')
                            ->maxLength(100),

                        TextInput::make('subtitle')
                            ->label('Sous-titre')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Image')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Image principale')
                            ->image()
                            ->directory('spotlights')
                            ->disk(config('filesystems.default', 'public'))
                            ->maxSize(3072)
                            ->imageEditor()
                            ->imageEditorAspectRatios(['4:3', '1:1'])
                            ->helperText('Format recommandé : 600x800px')
                            ->columnSpanFull(),
                    ]),

                Section::make('Statistiques (3 chiffres clés)')
                    ->schema([
                        TextInput::make('stat1_value')->label('Valeur 1')->placeholder('1729'),
                        TextInput::make('stat1_label')->label('Label 1')->placeholder('Année de création'),
                        TextInput::make('stat2_value')->label('Valeur 2')->placeholder('6000+'),
                        TextInput::make('stat2_label')->label('Label 2')->placeholder('Guerrières'),
                        TextInput::make('stat3_value')->label('Valeur 3')->placeholder('2 siècles'),
                        TextInput::make('stat3_label')->label('Label 3')->placeholder('De légende'),
                    ])
                    ->columns(2),

                Section::make('Boutons d\'action')
                    ->schema([
                        TextInput::make('cta1_label')
                            ->label('Bouton principal — Texte')
                            ->placeholder('Découvrir l\'histoire'),
                        TextInput::make('cta1_url')
                            ->label('Bouton principal — Lien')
                            ->placeholder('/about'),
                        TextInput::make('cta2_label')
                            ->label('Bouton secondaire — Texte')
                            ->placeholder('Visiter Abomey'),
                        TextInput::make('cta2_url')
                            ->label('Bouton secondaire — Lien')
                            ->placeholder('/destinations'),
                    ])
                    ->columns(2),

                Section::make('Planification & Diffusion')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Actif')
                            ->helperText('Seul le spotlight actif le plus récent s\'affiche sur la home')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Priorité (plus petit = prioritaire)')
                            ->numeric()
                            ->default(0),

                        DatePicker::make('starts_at')
                            ->label('Début de diffusion')
                            ->helperText('Laisser vide pour diffuser immédiatement')
                            ->native(false),

                        DatePicker::make('ends_at')
                            ->label('Fin de diffusion')
                            ->helperText('Laisser vide pour diffuser indéfiniment')
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->disk(config('filesystems.default', 'public'))
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Spotlight&background=c1440e&color=fff'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                Tables\Columns\TextColumn::make('badge_text')
                    ->label('Badge')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Immédiat'),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Indéfini'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Priorité')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSpotlights::route('/'),
            'create' => Pages\CreateSpotlight::route('/create'),
            'edit'   => Pages\EditSpotlight::route('/{record}/edit'),
        ];
    }
}