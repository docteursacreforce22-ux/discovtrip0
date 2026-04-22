<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class HeroSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.hero-settings';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationLabel(): string
    {
        return 'Images Hero';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contenu';
    }

    public static function getNavigationSort(): int
    {
        return 10;
    }

    public array $hero_home_1       = [];
    public array $hero_home_2       = [];
    public array $hero_home_3       = [];
    public array $hero_offers       = [];
    public array $hero_destinations = [];
    public array $hero_about        = [];
    public array $hero_contact      = [];
    public array $hero_blog         = [];

    // ═══════════════════════════════════════════════════════
    // CORRECTION FILAMENT v5 : utiliser $this->form->fill()
    // L'ancienne méthode ($this->key = value) ne déclenche
    // pas l'initialisation interne du FileUpload — les fichiers
    // temporaires ne sont jamais déplacés et rien n'est sauvé.
    // ═══════════════════════════════════════════════════════

    public function mount(): void
    {
        $keys = [
            'hero_home_1', 'hero_home_2', 'hero_home_3',
            'hero_offers', 'hero_destinations',
            'hero_about', 'hero_contact', 'hero_blog',
        ];

        $data = [];
        foreach ($keys as $key) {
            $value = SiteSetting::where('key', $key)->value('value');
            $data[$key] = $value ? [$value] : [];
        }

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        // Utilise le disk par défaut configuré (.env FILESYSTEM_DISK)
        // → 'public' en local/Railway, 'cloudinary' quand les clés sont ajoutées
        $disk = config('filesystems.default', 'public');

        return $schema->components([

            Section::make('Page d\'accueil — Slideshow')
                ->description('3 images défilant automatiquement. Format recommandé : 1920×1080px, max 3 Mo.')
                ->icon('heroicon-o-home')
                ->schema([
                    FileUpload::make('hero_home_1')
                        ->label('Image 1 — principale')
                        ->image()->directory('hero')->disk($disk)
                        ->maxSize(3072)->imageEditor()
                        ->helperText('Ex : Vue panoramique Cotonou'),

                    FileUpload::make('hero_home_2')
                        ->label('Image 2')
                        ->image()->directory('hero')->disk($disk)
                        ->maxSize(3072)->imageEditor()
                        ->helperText('Ex : Route des Esclaves Ouidah'),

                    FileUpload::make('hero_home_3')
                        ->label('Image 3')
                        ->image()->directory('hero')->disk($disk)
                        ->maxSize(3072)->imageEditor()
                        ->helperText('Ex : Village lacustre Ganvié'),
                ])
                ->columns(3),

            Section::make('Autres pages')
                ->description('Image de fond des heroes. Format recommandé : 1920×600px, max 3 Mo.')
                ->icon('heroicon-o-rectangle-stack')
                ->schema([
                    FileUpload::make('hero_offers')
                        ->label('Expériences')
                        ->image()->directory('hero')->disk($disk)->maxSize(3072)->imageEditor(),

                    FileUpload::make('hero_destinations')
                        ->label('Destinations')
                        ->image()->directory('hero')->disk($disk)->maxSize(3072)->imageEditor(),

                    FileUpload::make('hero_about')
                        ->label('À propos')
                        ->image()->directory('hero')->disk($disk)->maxSize(3072)->imageEditor(),

                    FileUpload::make('hero_contact')
                        ->label('Contact')
                        ->image()->directory('hero')->disk($disk)->maxSize(3072)->imageEditor(),

                    FileUpload::make('hero_blog')
                        ->label('Blog')
                        ->image()->directory('hero')->disk($disk)->maxSize(3072)->imageEditor(),
                ])
                ->columns(3),
        ]);
    }

    // ═══════════════════════════════════════════════════════
    // CORRECTION FILAMENT v5 : utiliser $this->form->getState()
    // C'est cette méthode qui déclenche le déplacement des
    // fichiers temporaires vers leur destination finale.
    // ═══════════════════════════════════════════════════════

    public function save(): void
    {
        $data = $this->form->getState();

        $keys = [
            'hero_home_1', 'hero_home_2', 'hero_home_3',
            'hero_offers', 'hero_destinations',
            'hero_about', 'hero_contact', 'hero_blog',
        ];

        foreach ($keys as $key) {
            $value = $data[$key] ?? [];

            if (is_array($value)) {
                $value = $value[0] ?? null;
            }

            if ($value) {
                SiteSetting::updateOrCreate(
                    ['key'  => $key],
                    ['value' => $value, 'type' => 'image', 'description' => "Hero: {$key}"]
                );
                Cache::forget('hero.' . $key);
            }
        }

        Notification::make()
            ->title('Images hero sauvegardées !')
            ->success()
            ->send();
    }
}
