<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Filament\Resources\OfferResource\RelationManagers\TiersRelationManager;
use App\Models\Offer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;
    protected static ?string $modelLabel = 'Offre';
    protected static ?string $pluralModelLabel = 'Offres';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-sparkles';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contenu';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Offer::onPromo()->count();
        return $count > 0 ? '🔥 ' . $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ══════════════════════════════════════════════════════
    // GROQ AI
    // ══════════════════════════════════════════════════════

    protected static function callGroq(string $systemPrompt, string $userPrompt, int $maxTokens = 800): ?string
    {
        $apiKey = config('services.groq.key');

        if (! $apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => config('services.groq.model', 'llama-3.3-70b-versatile'),
                'max_tokens'  => $maxTokens,
                'temperature' => 0.75,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
            ]);

            if (! $response->successful()) {
                return null;
            }

            return $response->json('choices.0.message.content');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected static function systemPrompt(): string
    {
        return 'Tu es un expert en rédaction de contenu touristique pour DiscovTrip, '
             . 'une agence spécialisée dans les expériences authentiques au Bénin (Afrique de l\'Ouest). '
             . 'Ton style est chaleureux, évocateur, professionnel. '
             . 'Tu écris TOUJOURS en français, sans astérisques, sans markdown, sans préambule. '
             . 'Tu vas directement au contenu demandé, rien d\'autre.';
    }

    protected static function buildContext(Get $get, array $extra = []): string
    {
        $parts = [];

        if ($t = $get('title'))            $parts[] = "Titre : {$t}";
        if ($c = $get('category'))         $parts[] = "Catégorie : {$c}";
        if ($d = $get('duration_minutes')) $parts[] = "Durée : {$d} minutes";

        $cityId = $get('city_id');
        if ($cityId) {
            $city = \App\Models\City::find($cityId);
            if ($city) $parts[] = "Ville : {$city->name}";
        }

        foreach ($extra as $k => $v) {
            if ($v) $parts[] = "{$k} : {$v}";
        }

        return $parts ? 'Contexte : ' . implode(' | ', $parts) : '';
    }

    // ══════════════════════════════════════════════════════
    // FORM
    // ══════════════════════════════════════════════════════

    public static function form(Schema $schema): Schema
    {
        // On utilise \Filament\Forms\Components\Actions\Action::make() en FQCN
        // pour éviter tout conflit avec Filament\Actions\* déjà importés
        $aiAction = fn(string $name, string $label, \Closure $action) =>
            \Filament\Forms\Components\Actions\Action::make($name)
                ->label($label)
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->action($action);

        return $schema->components([

            // ════════════════════════════════════
            // BANDEAU IA — Génération complète
            // ════════════════════════════════════
            Section::make('')
                ->schema([
                    \Filament\Forms\Components\Actions::make([

                        \Filament\Forms\Components\Actions\Action::make('ai_generate_all')
                            ->label('✨ Tout générer avec l\'IA')
                            ->color('warning')
                            ->icon('heroicon-o-sparkles')
                            ->requiresConfirmation()
                            ->modalHeading('Générer tout le contenu avec l\'IA')
                            ->modalDescription('L\'IA va générer titre, descriptions, inclus/exclus et FAQ depuis la Ville et la Catégorie sélectionnées.')
                            ->modalSubmitActionLabel('Générer')
                            ->action(function (Get $get, Set $set) {
                                $ctx = self::buildContext($get);

                                if (! $ctx) {
                                    Notification::make()
                                        ->title('Sélectionnez au moins une Ville et une Catégorie avant de générer.')
                                        ->warning()->send();
                                    return;
                                }

                                $result = self::callGroq(
                                    self::systemPrompt(),
                                    "{$ctx}\n\n"
                                    . "Génère un contenu complet pour cette offre touristique béninoise.\n"
                                    . "Utilise EXACTEMENT ces balises :\n\n"
                                    . "[TITRE]titre accrocheur 5-10 mots[/TITRE]\n"
                                    . "[ACCROCHE]2 phrases max 30 mots[/ACCROCHE]\n"
                                    . "[DESCRIPTION]200-300 mots 3-4 paragraphes sans bullet points[/DESCRIPTION]\n"
                                    . "[INCLUS]\n- item\n(5 à 8 items)\n[/INCLUS]\n"
                                    . "[EXCLUS]\n- item\n(4 à 6 items)\n[/EXCLUS]\n"
                                    . "[FAQ]\nQ: question\nR: réponse\n(5 paires)\n[/FAQ]\n\n"
                                    . "Aucun texte en dehors des balises.",
                                    2000
                                );

                                if (! $result) {
                                    Notification::make()
                                        ->title('Erreur API Groq — vérifiez GROQ_API_KEY et php artisan config:clear')
                                        ->danger()->send();
                                    return;
                                }

                                $extract = fn(string $tag) => preg_match(
                                    '/\[' . $tag . '\]([\s\S]*?)\[\/' . $tag . '\]/i',
                                    $result, $m
                                ) ? trim($m[1]) : null;

                                if ($v = $extract('TITRE')) {
                                    $set('title', $v);
                                    $set('slug', Str::slug($v));
                                }
                                if ($v = $extract('ACCROCHE'))    $set('description', $v);
                                if ($v = $extract('DESCRIPTION')) {
                                    $html = implode('', array_map(
                                        fn($p) => $p ? "<p>{$p}</p>" : '',
                                        explode("\n\n", trim($v))
                                    ));
                                    $set('long_description', $html ?: "<p>{$v}</p>");
                                }
                                if ($v = $extract('INCLUS')) {
                                    $items = array_values(array_filter(
                                        array_map(fn($l) => trim(ltrim(trim($l), '-•◦ ')), explode("\n", $v))
                                    ));
                                    $set('included_items', $items);
                                }
                                if ($v = $extract('EXCLUS')) {
                                    $items = array_values(array_filter(
                                        array_map(fn($l) => trim(ltrim(trim($l), '-•◦ ')), explode("\n", $v))
                                    ));
                                    $set('excluded_items', $items);
                                }
                                if ($v = $extract('FAQ')) {
                                    $faqItems = [];
                                    preg_match_all('/Q:\s*(.+)\nR:\s*(.+)/U', $v, $matches, PREG_SET_ORDER);
                                    foreach ($matches as $m) {
                                        $faqItems[] = ['q' => trim($m[1]), 'r' => trim($m[2])];
                                    }
                                    if ($faqItems) $set('faq', $faqItems);
                                }

                                Notification::make()
                                    ->title('✨ Contenu généré avec succès !')
                                    ->success()->send();
                            }),

                    ])->fullWidth(),
                ])
                ->extraAttributes([
                    'style' => 'background:linear-gradient(135deg,rgba(193,68,14,.06),rgba(212,146,74,.04));'
                             . 'border:1.5px solid rgba(193,68,14,.2);border-radius:12px;',
                ]),

            // ════════════════════════════════════
            // INFORMATIONS PRINCIPALES
            // ════════════════════════════════════
            Section::make('Informations principales')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    TextInput::make('title')
                        ->label('Titre de l\'offre')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, Set $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_title')
                                ->label('✨ Générer')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nGénère 3 titres d'offre touristique accrocheurs (5-10 mots).\nFormat :\n1. Titre\n2. Titre\n3. Titre\nAucun autre texte.",
                                        150
                                    );
                                    if ($result) {
                                        preg_match('/1[.\-)]\s*(.+)/m', $result, $m);
                                        $title = isset($m[1]) ? trim($m[1]) : trim(explode("\n", $result)[0]);
                                        $title = preg_replace('/^\d+[.\-)]\s*/', '', $title);
                                        $set('title', $title);
                                        $set('slug', Str::slug($title));
                                        Notification::make()->title('Titre généré !')->success()->send();
                                    } else {
                                        Notification::make()->title('Erreur Groq — vérifiez config:clear')->danger()->send();
                                    }
                                })
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('city_id')
                        ->label('Ville')
                        ->relationship('city', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Select::make('country_id')
                                ->label('Pays')
                                ->relationship('country', 'name')
                                ->required(),
                            TextInput::make('name')
                                ->label('Nom')
                                ->required(),
                        ]),

                    Select::make('category')
                        ->label('Catégorie')
                        ->options([
                            'cultural'   => '🕌 Culture',
                            'gastronomy' => '🍽️ Gastronomie',
                            'adventure'  => '🏔️ Aventure',
                            'wellness'   => '🧘 Bien-être',
                            'nature'     => '🌊 Nature',
                            'urban'      => '🏙️ Urbain',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->columns(2),

            // ════════════════════════════════════
            // DESCRIPTION
            // ════════════════════════════════════
            Section::make('Description')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Textarea::make('description')
                        ->label('Accroche courte')
                        ->required()
                        ->rows(3)
                        ->maxLength(65535)
                        ->columnSpanFull()
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_short_desc')
                                ->label('✨ Générer l\'accroche')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nRédige une accroche courte (2 phrases max, 30 mots max). Donne immédiatement envie, mentionne la destination et l'émotion principale. Aucun commentaire.",
                                        150
                                    );
                                    if ($result) {
                                        $set('description', trim($result));
                                        Notification::make()->title('Accroche générée !')->success()->send();
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),

                    RichEditor::make('long_description')
                        ->label('Description détaillée')
                        ->toolbarButtons(['bold','bulletList','italic','orderedList','redo','undo'])
                        ->columnSpanFull()
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_long_desc')
                                ->label('✨ Générer la description')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get, [
                                        'Accroche' => $get('description'),
                                    ]);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nRédige une description immersive de 200-300 mots.\n"
                                        . "Structure : 1 paragraphe d'accroche émotionnelle, 1 paragraphe sur le déroulement, 1 paragraphe sur l'unicité au Bénin, 1 phrase finale invitant à réserver.\n"
                                        . "Ton chaleureux, vivant. Pas de bullet points. Pas de titres.",
                                        600
                                    );
                                    if ($result) {
                                        $html = implode('', array_map(
                                            fn($p) => $p ? "<p>{$p}</p>" : '',
                                            explode("\n\n", trim($result))
                                        ));
                                        $set('long_description', $html ?: "<p>{$result}</p>");
                                        Notification::make()->title('Description générée !')->success()->send();
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),
                ]),

            // ════════════════════════════════════
            // IMAGES
            // ════════════════════════════════════
            Section::make('Images')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->directory('offers/covers')
                        ->disk('public')
                        ->maxSize(2048)
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9','4:3'])
                        ->helperText('Format recommandé : 1200x800px'),

                    FileUpload::make('gallery')
                        ->label('Galerie d\'images')
                        ->multiple()
                        ->image()
                        ->directory('offers/gallery')
                        ->disk('public')
                        ->maxSize(2048)
                        ->maxFiles(10)
                        ->imageEditor()
                        ->reorderable()
                        ->helperText('Jusqu\'à 10 images'),
                ])
                ->columns(2),

            // ════════════════════════════════════
            // TARIFICATION
            // ════════════════════════════════════
            Section::make('Tarification')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    TextInput::make('base_price')
                        ->label('Prix de base (FCFA)')
                        ->required()
                        ->numeric()
                        ->prefix('FCFA')
                        ->step(1000)
                        ->minValue(0)
                        ->live(onBlur: true)
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_price')
                                ->label('✨ Suggérer un prix')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nSuggère un prix de base réaliste en FCFA pour cette expérience béninoise.\nRéponds UNIQUEMENT avec le nombre entier arrondi à 500 FCFA près, sans espace ni symbole.\nExemple : 25000",
                                        30
                                    );
                                    if ($result) {
                                        $price = preg_replace('/\D/', '', trim($result));
                                        if ($price) {
                                            $set('base_price', (int) $price);
                                            Notification::make()
                                                ->title('Prix suggéré : ' . number_format((int)$price, 0, ',', ' ') . ' FCFA')
                                                ->success()->send();
                                        }
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),

                    TextInput::make('discount_percentage')
                        ->label('Réduction rapide (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->helperText('Affichage uniquement — utilisez la section Promo pour le vrai prix'),
                ])
                ->columns(2),

            // ════════════════════════════════════
            // 🔥 PROMO
            // ════════════════════════════════════
            Section::make('🔥 Promotion')
                ->description('Définissez un prix promotionnel avec dates de validité.')
                ->icon('heroicon-o-fire')
                ->schema([
                    Placeholder::make('promo_status')
                        ->label('Statut actuel')
                        ->content(function (?Offer $record): string {
                            if (! $record) return '—';
                            if ($record->is_promo) {
                                $discount = $record->promo_discount;
                                $ends = $record->promotion_ends_at
                                    ? ' · expire le ' . $record->promotion_ends_at->format('d/m/Y')
                                    : ' · sans date de fin';
                                return "✅ PROMO ACTIVE — −{$discount}%{$ends}";
                            }
                            if ($record->promotional_price && $record->promotion_ends_at?->lt(now())) {
                                return "⏰ Promo expirée le " . $record->promotion_ends_at->format('d/m/Y H:i');
                            }
                            if ($record->promotional_price && $record->promotion_starts_at?->gt(now())) {
                                return "⏳ Promo programmée — démarre le " . $record->promotion_starts_at->format('d/m/Y H:i');
                            }
                            return '— Aucune promotion configurée';
                        })
                        ->columnSpanFull()
                        ->visibleOn('edit'),

                    TextInput::make('promotional_price')
                        ->label('Prix promotionnel (FCFA)')
                        ->numeric()
                        ->prefix('FCFA')
                        ->step(500)
                        ->minValue(0)
                        ->live(onBlur: true)
                        ->helperText('Doit être inférieur au prix de base')
                        ->rules(['nullable', 'numeric', 'min:0']),

                    Placeholder::make('promo_discount_preview')
                        ->label('Réduction calculée')
                        ->content(function (Get $get): string {
                            $base  = (float) ($get('base_price') ?? 0);
                            $promo = (float) ($get('promotional_price') ?? 0);
                            if ($base > 0 && $promo > 0 && $promo < $base) {
                                $pct     = round((1 - $promo / $base) * 100);
                                $economy = number_format($base - $promo, 0, ',', ' ');
                                return "−{$pct}% · économie de {$economy} FCFA";
                            }
                            return '—';
                        }),

                    DateTimePicker::make('promotion_starts_at')
                        ->label('Début de la promotion')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->placeholder('Immédiatement si vide'),

                    DateTimePicker::make('promotion_ends_at')
                        ->label('Fin de la promotion')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->placeholder('Aucune limite si vide')
                        ->after('promotion_starts_at'),

                    Textarea::make('promo_description')
                        ->label('Message promotionnel')
                        ->placeholder('ex: Réservez maintenant et économisez 30% !')
                        ->rows(2)
                        ->maxLength(300)
                        ->columnSpanFull()
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_promo_desc')
                                ->label('✨ Générer le message')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx   = self::buildContext($get);
                                    $base  = (float) $get('base_price');
                                    $promo = (float) $get('promotional_price');
                                    $pct   = ($base > 0 && $promo > 0 && $promo < $base)
                                        ? round((1 - $promo / $base) * 100) . '%'
                                        : '';
                                    $extra = $pct ? " | Réduction : −{$pct}" : '';
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}{$extra}\n\nRédige un message promotionnel percutant en 1-2 phrases (30 mots max). Crée l'urgence{$extra}. Aucun commentaire.",
                                        100
                                    );
                                    if ($result) {
                                        $set('promo_description', trim($result));
                                        Notification::make()->title('Message généré !')->success()->send();
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(fn (?Offer $record) => ! ($record?->promotional_price)),

            // ════════════════════════════════════
            // DÉTAILS PRATIQUES
            // ════════════════════════════════════
            Section::make('Détails pratiques')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    TextInput::make('duration_minutes')
                        ->label('Durée (minutes)')
                        ->required()
                        ->numeric()
                        ->step(30)
                        ->minValue(30)
                        ->helperText('Ex: 240 pour 4h')
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_duration')
                                ->label('✨ Suggérer')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nSuggère une durée en minutes pour cette expérience (multiple de 30, entre 60 et 480).\nRéponds UNIQUEMENT avec le nombre entier. Exemple : 180",
                                        20
                                    );
                                    if ($result) {
                                        $minutes = (int) preg_replace('/\D/', '', trim($result));
                                        if ($minutes >= 30) {
                                            $set('duration_minutes', $minutes);
                                            $h = floor($minutes / 60);
                                            $m = $minutes % 60;
                                            Notification::make()
                                                ->title("Durée suggérée : {$h}h" . ($m ? "{$m}min" : ''))
                                                ->success()->send();
                                        }
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),

                    TextInput::make('min_participants')
                        ->label('Participants min.')
                        ->numeric()
                        ->default(1)
                        ->minValue(1),

                    TextInput::make('max_participants')
                        ->label('Participants max.')
                        ->required()
                        ->numeric()
                        ->default(10)
                        ->minValue(1)
                        ->maxValue(100),

                    TextInput::make('min_age')
                        ->label('Âge minimum')
                        ->numeric()
                        ->minValue(0),

                    Select::make('difficulty_level')
                        ->label('Difficulté')
                        ->options([
                            'easy'        => '🟢 Facile',
                            'moderate'    => '🟡 Modéré',
                            'challenging' => '🟠 Difficile',
                            'expert'      => '🔴 Expert',
                        ])
                        ->default('easy')
                        ->native(false),
                ])
                ->columns(5),

            // ════════════════════════════════════
            // INCLUSIONS
            // ════════════════════════════════════
            Section::make('Inclusions')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    TagsInput::make('included_items')
                        ->label('✅ Ce qui est inclus')
                        ->placeholder('Ajouter et appuyer Entrée')
                        ->helperText('Ex: Transport, Guide, Repas…')
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_included')
                                ->label('✨ Générer la liste')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nListe ce qui est INCLUS dans cette expérience (5 à 8 éléments courts et précis).\nFormat : un élément par ligne commençant par -\nAucun titre ni commentaire.",
                                        250
                                    );
                                    if ($result) {
                                        $items = array_values(array_filter(
                                            array_map(
                                                fn($l) => trim(ltrim(trim($l), '-•◦ ')),
                                                explode("\n", $result)
                                            )
                                        ));
                                        $set('included_items', $items);
                                        Notification::make()->title(count($items) . ' éléments inclus générés !')->success()->send();
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),

                    TagsInput::make('excluded_items')
                        ->label('❌ Ce qui n\'est PAS inclus')
                        ->placeholder('Ajouter et appuyer Entrée')
                        ->helperText('Ex: Boissons, Pourboires…')
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('ai_excluded')
                                ->label('✨ Générer la liste')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->action(function (Get $get, Set $set) {
                                    $ctx    = self::buildContext($get);
                                    $result = self::callGroq(
                                        self::systemPrompt(),
                                        "{$ctx}\n\nListe ce qui est EXCLU de cette expérience (4 à 6 éléments courts).\nFormat : un élément par ligne commençant par -\nAucun titre ni commentaire.",
                                        200
                                    );
                                    if ($result) {
                                        $items = array_values(array_filter(
                                            array_map(
                                                fn($l) => trim(ltrim(trim($l), '-•◦ ')),
                                                explode("\n", $result)
                                            )
                                        ));
                                        $set('excluded_items', $items);
                                        Notification::make()->title(count($items) . ' éléments exclus générés !')->success()->send();
                                    } else {
                                        Notification::make()->title('Erreur Groq API')->danger()->send();
                                    }
                                })
                        ),
                ])
                ->columns(2),

            // ════════════════════════════════════
            // FAQ
            // ════════════════════════════════════
            Section::make('Questions fréquentes (FAQ)')
                ->icon('heroicon-o-question-mark-circle')
                ->description('Si aucune n\'est définie, des FAQs génériques sont affichées.')
                ->headerActions([
                    \Filament\Forms\Components\Actions\Action::make('ai_faq')
                        ->label('✨ Générer la FAQ')
                        ->icon('heroicon-o-sparkles')
                        ->color('warning')
                        ->action(function (Get $get, Set $set) {
                            $ctx    = self::buildContext($get);
                            $result = self::callGroq(
                                self::systemPrompt(),
                                "{$ctx}\n\nGénère une FAQ de 5 questions-réponses sur cette expérience.\n"
                                . "Couvre : annulation, ce qu'il faut apporter, niveau requis, langue du guide, accessibilité.\n"
                                . "Format STRICT :\nQ: question\nR: réponse courte 1-2 phrases\n\n5 paires. Aucun autre texte.",
                                500
                            );
                            if ($result) {
                                $faqItems = [];
                                preg_match_all('/Q:\s*(.+)\nR:\s*(.+)/U', $result, $matches, PREG_SET_ORDER);
                                foreach ($matches as $m) {
                                    $faqItems[] = ['q' => trim($m[1]), 'r' => trim($m[2])];
                                }
                                if ($faqItems) {
                                    $set('faq', $faqItems);
                                    Notification::make()->title(count($faqItems) . ' questions générées !')->success()->send();
                                } else {
                                    Notification::make()->title('Format inattendu — réessayez')->warning()->send();
                                }
                            } else {
                                Notification::make()->title('Erreur Groq API')->danger()->send();
                            }
                        }),
                ])
                ->schema([
                    Repeater::make('faq')
                        ->label('')
                        ->schema([
                            TextInput::make('q')
                                ->label('Question')
                                ->required()
                                ->placeholder('ex: Que faut-il apporter ?')
                                ->columnSpanFull(),
                            Textarea::make('r')
                                ->label('Réponse')
                                ->required()
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->addActionLabel('+ Ajouter une question')
                        ->defaultItems(0)
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string =>
                            ! empty($state['q']) ? Str::limit($state['q'], 60) : 'Nouvelle question'
                        )
                        ->maxItems(8),
                ])
                ->collapsible()
                ->collapsed(),

            // ════════════════════════════════════
            // PUBLICATION
            // ════════════════════════════════════
            Section::make('Paramètres de publication')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Select::make('status')
                        ->options([
                            'draft'     => '⚫ Brouillon',
                            'published' => '🟢 Publié',
                            'archived'  => '🔴 Archivé',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),

                    Toggle::make('is_featured')
                        ->label('Mise en avant (Accueil)')
                        ->default(false),

                    Toggle::make('is_instant_booking')
                        ->label('Réservation instantanée')
                        ->default(false),

                    Select::make('payment_mode')
                        ->label('Mode de paiement')
                        ->options([
                            'on_site' => '📍 Sur place uniquement',
                            'online'  => '💳 En ligne uniquement',
                            'both'    => '🔄 Les deux au choix',
                        ])
                        ->default('on_site')
                        ->required()
                        ->native(false),

                    TextInput::make('available_spots')
                        ->label('Places disponibles')
                        ->numeric()
                        ->minValue(0)
                        ->helperText('Vide = illimité'),
                ])
                ->columns(5),
        ]);
    }

    // ══════════════════════════════════════════════════════
    // TABLE
    // ══════════════════════════════════════════════════════

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('')
                    ->circular()
                    ->disk('public')
                    ->size(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(35)
                    ->description(fn (Offer $record): string =>
                        $record->city->name . ' · ' . $record->category_label
                    ),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Prix')
                    ->sortable()
                    ->formatStateUsing(function (Offer $record): string {
                        if ($record->is_promo) {
                            $promo = number_format($record->promotional_price, 0, ',', ' ');
                            return "{$promo} FCFA";
                        }
                        return number_format($record->base_price, 0, ',', ' ') . ' FCFA';
                    })
                    ->description(fn (Offer $record): ?string =>
                        $record->is_promo
                            ? '🔥 −' . $record->promo_discount . '% · était ' . number_format($record->base_price, 0, ',', ' ') . ' FCFA'
                            : null
                    )
                    ->color(fn (Offer $record): string => $record->is_promo ? 'danger' : 'gray'),

                Tables\Columns\BadgeColumn::make('promo_status')
                    ->label('Promo')
                    ->getStateUsing(function (Offer $record): string {
                        if ($record->is_promo) return 'Active';
                        if ($record->promotional_price && $record->promotion_ends_at?->lt(now())) return 'Expirée';
                        if ($record->promotional_price && $record->promotion_starts_at?->gt(now())) return 'Programmée';
                        return 'Aucune';
                    })
                    ->colors([
                        'success' => 'Active',
                        'warning' => 'Programmée',
                        'danger'  => 'Expirée',
                        'gray'    => 'Aucune',
                    ]),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('⭐')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => 'Brouillon',
                        'published' => 'Publié',
                        'archived'  => 'Archivé',
                        default     => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'published' => 'success',
                        'archived'  => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('promotion_ends_at')
                    ->label('Fin promo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color(fn (?string $state): string =>
                        $state && \Carbon\Carbon::parse($state)->lt(now()) ? 'danger' : 'gray'
                    )
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Brouillon',
                        'published' => 'Publié',
                        'archived'  => 'Archivé',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'cultural'   => '🕌 Culture',
                        'gastronomy' => '🍽️ Gastronomie',
                        'adventure'  => '🏔️ Aventure',
                        'wellness'   => '🧘 Bien-être',
                        'nature'     => '🌊 Nature',
                        'urban'      => '🏙️ Urbain',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mise en avant'),
                Tables\Filters\SelectFilter::make('city')
                    ->relationship('city', 'name')
                    ->preload(),
                Tables\Filters\Filter::make('on_promo')
                    ->label('🔥 En promotion')
                    ->query(fn (Builder $query) => $query->onPromo())
                    ->toggle(),
                Tables\Filters\Filter::make('promo_expired')
                    ->label('⏰ Promo expirée')
                    ->query(fn (Builder $query) => $query->promoExpired())
                    ->toggle(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            TiersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit'   => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}