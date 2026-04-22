<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Filament\Resources\OfferResource\RelationManagers\TiersRelationManager;
use App\Models\Offer;
use Filament\Actions\Action;
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
use Filament\Schemas\Components\Actions as SchemaActions;
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

    protected static function callGroq(string $system, string $user, int $maxTokens = 800): ?string
    {
        $apiKey = config('services.groq.key');
        if (! $apiKey) return null;
        try {
            $r = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => config('services.groq.model', 'llama-3.3-70b-versatile'),
                'max_tokens'  => $maxTokens,
                'temperature' => 0.75,
                'messages'    => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user',   'content' => $user],
                ],
            ]);
            return $r->successful() ? $r->json('choices.0.message.content') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected static function sys(): string
    {
        return 'Tu es un expert en rédaction touristique pour DiscovTrip, agence d\'expériences authentiques au Bénin (Afrique de l\'Ouest). '
             . 'Style chaleureux, évocateur, professionnel. Toujours en français, sans astérisques ni markdown. '
             . 'Va directement au contenu, rien d\'autre.';
    }

    protected static function ctx(Get $get, array $extra = []): string
    {
        $p = [];
        if ($v = $get('title'))            $p[] = "Titre: {$v}";
        if ($v = $get('category'))         $p[] = "Catégorie: {$v}";
        if ($v = $get('duration_minutes')) $p[] = "Durée: {$v} min";
        if ($id = $get('city_id')) {
            $city = \App\Models\City::find($id);
            if ($city) $p[] = "Ville: {$city->name}";
        }
        foreach ($extra as $k => $v) { if ($v) $p[] = "{$k}: {$v}"; }
        return $p ? 'Contexte — ' . implode(' | ', $p) : '';
    }

    // ══════════════════════════════════════════════════════
    // FORM
    // ══════════════════════════════════════════════════════

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            // ════════════════════════════════════
            // ASSISTANT IA
            // ════════════════════════════════════
            Section::make('✨ Assistant IA')
                ->description('Générez automatiquement le contenu de l\'offre. Renseignez d\'abord Ville et Catégorie.')
                ->icon('heroicon-o-sparkles')
                ->schema([
                    SchemaActions::make([

                        Action::make('ai_all')
                            ->label('🚀 Tout générer')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->modalHeading('Générer tout le contenu')
                            ->modalDescription('Titre, accroche, description, inclus/exclus et FAQ seront générés depuis Ville + Catégorie.')
                            ->modalSubmitActionLabel('Générer')
                            ->action(function (Get $get, Set $set) {
                                $ctx = self::ctx($get);
                                if (! $ctx) {
                                    Notification::make()->title('Renseignez Ville et Catégorie d\'abord.')->warning()->send();
                                    return;
                                }
                                $result = self::callGroq(self::sys(),
                                    "{$ctx}\n\nGénère le contenu complet avec EXACTEMENT ces balises :\n"
                                    . "[TITRE]5-10 mots[/TITRE]\n"
                                    . "[ACCROCHE]2 phrases max[/ACCROCHE]\n"
                                    . "[DESCRIPTION]200-300 mots, 3-4 paragraphes[/DESCRIPTION]\n"
                                    . "[INCLUS]\n- item\n[/INCLUS]\n"
                                    . "[EXCLUS]\n- item\n[/EXCLUS]\n"
                                    . "[FAQ]\nQ: question\nR: réponse\n[/FAQ]\n"
                                    . "Aucun texte hors balises.", 2000);
                                if (! $result) {
                                    Notification::make()->title('Erreur Groq — vérifiez GROQ_API_KEY puis php artisan config:clear')->danger()->send();
                                    return;
                                }
                                $extract = fn(string $tag) => preg_match('/\[' . $tag . '\]([\s\S]*?)\[\/' . $tag . '\]/i', $result, $m) ? trim($m[1]) : null;
                                if ($v = $extract('TITRE'))       { $set('title', $v); $set('slug', Str::slug($v)); }
                                if ($v = $extract('ACCROCHE'))    $set('description', $v);
                                if ($v = $extract('DESCRIPTION')) {
                                    $html = implode('', array_map(fn($p) => $p ? "<p>{$p}</p>" : '', explode("\n\n", trim($v))));
                                    $set('long_description', $html ?: "<p>{$v}</p>");
                                }
                                if ($v = $extract('INCLUS'))
                                    $set('included_items', array_values(array_filter(array_map(fn($l) => trim(ltrim(trim($l), '-•◦ ')), explode("\n", $v)))));
                                if ($v = $extract('EXCLUS'))
                                    $set('excluded_items', array_values(array_filter(array_map(fn($l) => trim(ltrim(trim($l), '-•◦ ')), explode("\n", $v)))));
                                if ($v = $extract('FAQ')) {
                                    $faq = [];
                                    preg_match_all('/Q:\s*(.+)\nR:\s*(.+)/U', $v, $matches, PREG_SET_ORDER);
                                    foreach ($matches as $m) $faq[] = ['q' => trim($m[1]), 'r' => trim($m[2])];
                                    if ($faq) $set('faq', $faq);
                                }
                                Notification::make()->title('✨ Contenu généré avec succès !')->success()->send();
                            }),

                        Action::make('ai_title')
                            ->label('📝 Titre')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $result = self::callGroq(self::sys(),
                                    self::ctx($get) . "\n\nGénère 3 titres accrocheurs (5-10 mots).\nFormat :\n1. Titre\n2. Titre\n3. Titre\nAucun autre texte.", 150);
                                if ($result) {
                                    preg_match('/1[.\-)]\s*(.+)/m', $result, $m);
                                    $t = isset($m[1]) ? trim(preg_replace('/^\d+[.\-)]\s*/', '', $m[1])) : trim(explode("\n", $result)[0]);
                                    $set('title', $t);
                                    $set('slug', Str::slug($t));
                                    Notification::make()->title('Titre généré !')->success()->send();
                                } else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                        Action::make('ai_accroche')
                            ->label('💬 Accroche')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $result = self::callGroq(self::sys(),
                                    self::ctx($get) . "\n\nRédige une accroche courte (2 phrases max, 30 mots). Donne envie, mentionne destination et émotion principale.", 150);
                                if ($result) { $set('description', trim($result)); Notification::make()->title('Accroche générée !')->success()->send(); }
                                else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                        Action::make('ai_description')
                            ->label('📄 Description')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $result = self::callGroq(self::sys(),
                                    self::ctx($get, ['Accroche' => $get('description')])
                                    . "\n\nRédige une description immersive 200-300 mots. "
                                    . "Structure : accroche émotionnelle, déroulement, unicité au Bénin, invitation à réserver. "
                                    . "Pas de bullet points ni titres.", 600);
                                if ($result) {
                                    $html = implode('', array_map(fn($p) => $p ? "<p>{$p}</p>" : '', explode("\n\n", trim($result))));
                                    $set('long_description', $html ?: "<p>{$result}</p>");
                                    Notification::make()->title('Description générée !')->success()->send();
                                } else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                        Action::make('ai_inclus')
                            ->label('✅ Inclus')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $result = self::callGroq(self::sys(),
                                    self::ctx($get) . "\n\nListe ce qui est INCLUS (5-8 items courts).\nFormat: un item par ligne commençant par -\nAucun titre.", 250);
                                if ($result) {
                                    $items = array_values(array_filter(array_map(fn($l) => trim(ltrim(trim($l), '-•◦ ')), explode("\n", $result))));
                                    $set('included_items', $items);
                                    Notification::make()->title(count($items) . ' inclus générés !')->success()->send();
                                } else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                        Action::make('ai_exclus')
                            ->label('❌ Exclus')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $result = self::callGroq(self::sys(),
                                    self::ctx($get) . "\n\nListe ce qui est EXCLU (4-6 items courts).\nFormat: un item par ligne commençant par -\nAucun titre.", 200);
                                if ($result) {
                                    $items = array_values(array_filter(array_map(fn($l) => trim(ltrim(trim($l), '-•◦ ')), explode("\n", $result))));
                                    $set('excluded_items', $items);
                                    Notification::make()->title(count($items) . ' exclus générés !')->success()->send();
                                } else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                        Action::make('ai_faq')
                            ->label('❓ FAQ')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $result = self::callGroq(self::sys(),
                                    self::ctx($get) . "\n\nGénère 5 questions-réponses FAQ.\nCouvre: annulation, à apporter, niveau requis, langue guide, accessibilité.\nFormat STRICT:\nQ: question\nR: réponse 1-2 phrases\n\n5 paires. Aucun autre texte.", 500);
                                if ($result) {
                                    $faq = [];
                                    preg_match_all('/Q:\s*(.+)\nR:\s*(.+)/U', $result, $matches, PREG_SET_ORDER);
                                    foreach ($matches as $m) $faq[] = ['q' => trim($m[1]), 'r' => trim($m[2])];
                                    if ($faq) { $set('faq', $faq); Notification::make()->title(count($faq) . ' questions générées !')->success()->send(); }
                                    else Notification::make()->title('Format inattendu — réessayez')->warning()->send();
                                } else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                        Action::make('ai_prix')
                            ->label('💰 Prix & Durée')
                            ->color('gray')
                            ->action(function (Get $get, Set $set) {
                                $ctx = self::ctx($get);
                                $rPrice = self::callGroq(self::sys(), "{$ctx}\n\nSuggère un prix en FCFA (arrondi à 500). UNIQUEMENT le nombre entier sans symbole. Ex: 25000", 20);
                                $rDur   = self::callGroq(self::sys(), "{$ctx}\n\nSuggère une durée en minutes (multiple de 30, entre 60 et 480). UNIQUEMENT le nombre. Ex: 180", 20);
                                $msg = [];
                                if ($rPrice) { $price = (int) preg_replace('/\D/', '', trim($rPrice)); if ($price) { $set('base_price', $price); $msg[] = number_format($price, 0, ',', ' ') . ' FCFA'; } }
                                if ($rDur)   { $min = (int) preg_replace('/\D/', '', trim($rDur)); if ($min >= 30) { $set('duration_minutes', $min); $msg[] = floor($min/60) . 'h' . ($min%60 ? $min%60 . 'min' : ''); } }
                                if ($msg) Notification::make()->title('Suggéré : ' . implode(' · ', $msg))->success()->send();
                                else Notification::make()->title('Erreur Groq API')->danger()->send();
                            }),

                    ])->fullWidth(),
                ])
                ->collapsible()
                ->persistCollapsed(),

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
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }
                        }),

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
                        ->label('Description courte (accroche)')
                        ->required()
                        ->rows(3)
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    RichEditor::make('long_description')
                        ->label('Description détaillée')
                        ->toolbarButtons(['bold','bulletList','italic','orderedList','redo','undo'])
                        ->columnSpanFull(),
                ]),

            // ════════════════════════════════════
            // IMAGES & VIDÉO
            // ════════════════════════════════════
            Section::make('Images & Vidéo')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->directory('offers/covers')
                        ->disk(config('filesystems.default', 'public'))
                        ->maxSize(2048)
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9','4:3'])
                        ->helperText('Format recommandé : 1200x800px'),

                    FileUpload::make('gallery')
                        ->label('Galerie d\'images')
                        ->multiple()
                        ->image()
                        ->directory('offers/gallery')
                        ->disk(config('filesystems.default', 'public'))
                        ->maxSize(2048)
                        ->maxFiles(10)
                        ->imageEditor()
                        ->reorderable()
                        ->helperText('Jusqu\'à 10 images')
                        ->columnSpanFull(),

                    TextInput::make('video_url')
                        ->label('Vidéo (URL YouTube ou Vimeo)')
                        ->url()
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->helperText('YouTube ou Vimeo uniquement — la vidéo remplace la photo principale dans la galerie si renseignée.')
                        ->prefix('🎬')
                        ->columnSpanFull(),
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
                        ->live(onBlur: true),

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
                ->description('Prix promotionnel avec dates de validité. Actif automatiquement entre les dates choisies.')
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
                            if ($record->promotional_price && $record->promotion_ends_at?->lt(now()))
                                return "⏰ Promo expirée le " . $record->promotion_ends_at->format('d/m/Y H:i');
                            if ($record->promotional_price && $record->promotion_starts_at?->gt(now()))
                                return "⏳ Promo programmée — démarre le " . $record->promotion_starts_at->format('d/m/Y H:i');
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
                        ->helperText('Doit être inférieur au prix de base'),

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
                        ->placeholder('ex: Réservez maintenant et économisez 30% sur votre aventure au Bénin !')
                        ->rows(2)
                        ->maxLength(300)
                        ->columnSpanFull(),
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
                        ->helperText('Ex: 240 pour 4h'),

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

                    TagsInput::make('languages')
                        ->label('Langues du guide')
                        ->placeholder('ex: Français, Anglais')
                        ->helperText('Appuyer Entrée après chaque langue')
                        ->suggestions(['Français', 'Anglais', 'Espagnol', 'Allemand', 'Fon', 'Yoruba']),

                    TextInput::make('meeting_point')
                        ->label('Point de rendez-vous')
                        ->placeholder('ex: Parvis de la cathédrale de Cotonou')
                        ->helperText('Adresse ou description précise du lieu de départ')
                        ->prefix('📍')
                        ->columnSpanFull(),
                ])
                ->columns(3),

            // ════════════════════════════════════
            // GUIDE
            // ════════════════════════════════════
            Section::make('Guide & Accompagnement')
                ->icon('heroicon-o-user-circle')
                ->description('Définissez comment votre équipe accompagne les visiteurs sur cette expérience.')
                ->schema([
                    Select::make('guide_type')
                        ->label('Type d\'accompagnement')
                        ->options([
                            'agency'   => '🏢 Guide agence (assigné après confirmation)',
                            'assigned' => '👤 Guide prédéfini (profil visible)',
                            'on_site'  => '🏛️ Guide local du site (DiscovTrip = accompagnateur)',
                        ])
                        ->default('agency')
                        ->required()
                        ->native(false)
                        ->live()
                        ->helperText(function (Get $get): string {
                            return match ($get('guide_type')) {
                                'agency'   => 'Un guide certifié DiscovTrip sera assigné après confirmation de la réservation.',
                                'assigned' => 'Le profil du guide sera visible sur la page de l\'offre. Sélectionnez le guide ci-dessous.',
                                'on_site'  => 'Le site dispose de son propre guide officiel. L\'équipe DiscovTrip assure le transfert et l\'accompagnement logistique.',
                                default    => '',
                            };
                        }),

                    Select::make('user_id')
                        ->label('Guide assigné')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'first_name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('role', 'guide')
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) =>
                            trim($record->first_name . ' ' . $record->last_name) . ' — ' . ($record->city->name ?? 'Bénin')
                        )
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->visible(fn (Get $get): bool => $get('guide_type') === 'assigned')
                        ->helperText('Sélectionnez un guide parmi les membres de l\'agence'),
                ])
                ->columns(2),

            // ════════════════════════════════════
            // INCLUSIONS
            // ════════════════════════════════════
            Section::make('Inclusions')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    TagsInput::make('included_items')
                        ->label('✅ Ce qui est inclus')
                        ->placeholder('Ajouter et appuyer Entrée')
                        ->helperText('Ex: Transport, Guide, Repas…'),

                    TagsInput::make('excluded_items')
                        ->label('❌ Ce qui n\'est PAS inclus')
                        ->placeholder('Ajouter et appuyer Entrée')
                        ->helperText('Ex: Boissons, Pourboires…'),
                ])
                ->columns(2),

            // ════════════════════════════════════
            // FAQ
            // ════════════════════════════════════
            Section::make('Questions fréquentes (FAQ)')
                ->icon('heroicon-o-question-mark-circle')
                ->description('Si aucune n\'est définie, des FAQs génériques sont affichées.')
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
            // PARAMÈTRES DE PUBLICATION
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('')
                    ->circular()
                    ->disk(config('filesystems.default', 'public'))
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

                Tables\Columns\TextColumn::make('guide_type')
                    ->label('Guide')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'assigned' => '👤 Prédéfini',
                        'agency'   => '🏢 Agence',
                        'on_site'  => '🏛️ Sur site',
                        default    => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'success',
                        'agency'   => 'info',
                        'on_site'  => 'warning',
                        default    => 'gray',
                    }),

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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Brouillon', 'published' => 'Publié', 'archived' => 'Archivé']),

                Tables\Filters\SelectFilter::make('guide_type')
                    ->label('Type de guide')
                    ->options([
                        'agency'   => '🏢 Agence',
                        'assigned' => '👤 Prédéfini',
                        'on_site'  => '🏛️ Sur site',
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

                Tables\Filters\TernaryFilter::make('is_featured')->label('Mise en avant'),
                Tables\Filters\SelectFilter::make('city')->relationship('city', 'name')->preload(),

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