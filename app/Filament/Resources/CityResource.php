<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class CityResource extends Resource
{
    protected static ?string $model = City::class;
    protected static ?string $modelLabel = 'Ville';
    protected static ?string $pluralModelLabel = 'Villes';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-map-pin';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Destinations';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── 1. INFORMATIONS DE BASE ──────────────────────────────
                Section::make('Informations de base')
                    ->schema([
                        Select::make('country_id')
                            ->label('Pays')
                            ->relationship('country', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Nom de la ville')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('region')
                            ->label('Région / Département')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->helperText('2–3 phrases évocatrices affichées sur la page Destinations et en hero de la page ville. Ex: "Ganvié flotte sur le lac Nokoué depuis le XVIIe siècle."')
                            ->rows(3)
                            ->maxLength(400)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ── 2. FEATURING & VISIBILITÉ ────────────────────────────
                Section::make('🌟 Featuring — Page d\'accueil')
                    ->description('Contrôle l\'affichage sur la home (max 5 recommandé) et la page Destinations.')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Ville active')
                            ->helperText('Une ville inactive n\'apparaît nulle part sur la vitrine.')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Mettre en avant (home)')
                            ->helperText('Affiche la ville dans la section Destinations de la home et en "À la une" sur la page Destinations.')
                            ->default(false),

                        TextInput::make('featured_order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(99)
                            ->minValue(1)
                            ->helperText('1 = affiché en premier.'),

                        FileUpload::make('cover_image')
                            ->label('Image de couverture')
                            ->helperText('Format recommandé : 800×600 px, ratio 4:3. JPEG ou WebP. Max 2 Mo.')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['4:3', '16:9'])
                            ->disk(config('filesystems.default', 'public'))
                            ->directory('cities/covers')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // ── 3. INFORMATIONS VOYAGEUR ─────────────────────────────
                Section::make('🗺️ Informations voyageur')
                    ->description('Ces données s\'affichent sur les cartes et en meta bar de la page ville.')
                    ->schema([
                        Select::make('category')
                            ->label('Catégorie')
                            ->options([
                                'urban'      => '🏙️ Urbain',
                                'historical' => '🏛️ Historique',
                                'nature'     => '🌿 Nature & Plage',
                                'coastal'    => '🏖️ Côtière',
                            ])
                            ->default('urban')
                            ->required()
                            ->helperText('Utilisé pour les filtres de la page Destinations.'),

                        TextInput::make('distance_from_cotonou')
                            ->label('Distance depuis Cotonou')
                            ->placeholder('Ex: 45 min, 2h30, Base')
                            ->helperText('Temps de trajet approximatif en voiture.')
                            ->maxLength(30),

                        TextInput::make('duration_days')
                            ->label('Durée recommandée')
                            ->placeholder('Ex: 1 jour, 2–3 jours')
                            ->helperText('Temps minimum pour bien profiter de la destination.')
                            ->maxLength(30),

                        TextInput::make('best_season')
                            ->label('Meilleure saison')
                            ->placeholder('Ex: Nov–Avr, Toute l\'année')
                            ->helperText('Période idéale de visite (badge sur la carte).')
                            ->maxLength(40),

                        TextInput::make('average_rating')
                            ->label('Note moyenne')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5)
                            ->suffix('/ 5')
                            ->helperText('Saisie manuelle. Calculée automatiquement depuis les avis quand disponibles.'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 4. CONTENU ÉDITORIAL ─────────────────────────────────
                Section::make('✍️ Contenu éditorial — Page ville')
                    ->description('Ces blocs enrichissent la page dédiée à la ville. Chaque bloc est optionnel : laissez vide = section masquée automatiquement sur la vitrine.')
                    ->schema([

                        // Pourquoi y aller
                        Repeater::make('highlights')
                            ->label('Pourquoi y aller — 3 raisons clés')
                            ->helperText('3 raisons courtes et percutantes qui donnent envie de visiter.')
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Icône FontAwesome')
                                    ->placeholder('Ex: landmark, fish, music, crown, leaf...')
                                    ->helperText('Nom sans "fa-". Référence : fontawesome.com/icons')
                                    ->maxLength(40),

                                TextInput::make('title')
                                    ->label('Titre court')
                                    ->placeholder('Ex: Histoire vivante, Gastronomie unique...')
                                    ->required()
                                    ->maxLength(60),

                                Textarea::make('description')
                                    ->label('Description (1–2 phrases)')
                                    ->rows(2)
                                    ->maxLength(160)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->maxItems(3)
                            ->addActionLabel('+ Ajouter une raison')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),

                        // Les incontournables
                        Repeater::make('landmarks')
                            ->label('Les incontournables — lieux à visiter')
                            ->helperText('4 à 5 lieux clés que le touriste ne doit pas manquer.')
                            ->schema([
                                TextInput::make('emoji')
                                    ->label('Emoji')
                                    ->placeholder('🕌')
                                    ->maxLength(10),

                                TextInput::make('name')
                                    ->label('Nom du lieu')
                                    ->placeholder('Ex: Route des Esclaves, Palais Royal...')
                                    ->required()
                                    ->maxLength(80),

                                Textarea::make('description')
                                    ->label('Description courte')
                                    ->rows(2)
                                    ->maxLength(180)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->maxItems(5)
                            ->addActionLabel('+ Ajouter un lieu')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),

                        // Infos pratiques
                        TextInput::make('how_to_get_there')
                            ->label('Comment y aller')
                            ->placeholder('Ex: Taxi-moto depuis Cotonou (45 min), bus STIF depuis Dantokpa...')
                            ->helperText('Transport depuis Cotonou ou la ville principale.')
                            ->maxLength(200)
                            ->columnSpanFull(),

                        TextInput::make('best_time_detail')
                            ->label('Meilleure période — détail')
                            ->placeholder('Ex: Novembre à avril, saison sèche. Évitez juin–septembre (pluies).')
                            ->helperText('Plus précis que le champ "Meilleure saison" ci-dessus.')
                            ->maxLength(200)
                            ->columnSpanFull(),

                        TextInput::make('budget_range')
                            ->label('Budget indicatif / jour')
                            ->placeholder('Ex: 25 000 – 70 000 FCFA / personne / jour')
                            ->maxLength(80),

                        // Le saviez-vous
                        Repeater::make('fun_facts')
                            ->label('Le saviez-vous ? — faits culturels marquants')
                            ->helperText('1 à 3 faits surprenants ou méconnus sur la ville. Affiché en citation.')
                            ->schema([
                                Textarea::make('fact')
                                    ->label('Fait culturel')
                                    ->placeholder('Ex: Ouidah est le berceau du vaudou mondial, pratiqué par 60 millions de personnes sur 4 continents.')
                                    ->rows(3)
                                    ->maxLength(220)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->maxItems(3)
                            ->addActionLabel('+ Ajouter un fait')
                            ->collapsible()
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // ── 5. GÉOLOCALISATION ───────────────────────────────────
                Section::make('📍 Géolocalisation')
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.0001),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.0001),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Image')
                    ->square()
                    ->size(52)
                    ->disk(config('filesystems.default', 'public'))
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=City&background=c1440e&color=fff'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Ville')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('country.name')
                    ->label('Pays')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'urban'      => 'info',
                        'historical' => 'warning',
                        'nature'     => 'success',
                        'coastal'    => 'primary',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'urban'      => '🏙️ Urbain',
                        'historical' => '🏛️ Historique',
                        'nature'     => '🌿 Nature',
                        'coastal'    => '🏖️ Côtière',
                        default      => $state ?? '—',
                    }),

                Tables\Columns\TextColumn::make('distance_from_cotonou')
                    ->label('Distance')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('best_season')
                    ->label('Saison')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Note')
                    ->suffix(' ⭐')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('offers_count')
                    ->label('Offres')
                    ->counts('offers')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('featured_order')
                    ->label('Ordre')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->relationship('country', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'urban'      => 'Urbain',
                        'historical' => 'Historique',
                        'nature'     => 'Nature',
                        'coastal'    => 'Côtière',
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mise en avant'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            ->defaultSort('featured_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit'   => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}