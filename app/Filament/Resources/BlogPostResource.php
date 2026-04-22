<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $modelLabel = 'Article';
    protected static ?string $pluralModelLabel = 'Blog';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-newspaper';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contenu';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function getNavigationBadge(): ?string
    {
        $drafts = BlogPost::where('status', 'draft')->count();
        return $drafts > 0 ? $drafts . ' brouillons' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Contenu principal')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Select::make('category')
                        ->label('Catégorie')
                        ->options([
                            'destinations' => '🗺️ Destinations',
                            'conseils'     => '💡 Conseils voyage',
                            'culture'      => '🎭 Culture & Traditions',
                            'pratique'     => '📋 Guide pratique',
                        ])
                        ->required()
                        ->native(false),

                    Textarea::make('excerpt')
                        ->label('Extrait (résumé court)')
                        ->rows(3)
                        ->maxLength(300)
                        ->helperText('Affiché dans les listes et les résultats de recherche. 280 car. max.')
                        ->columnSpanFull(),

                    RichEditor::make('content')
                        ->label('Contenu')
                        ->required()
                        ->toolbarButtons(['bold', 'bulletList', 'h2', 'h3', 'italic', 'link', 'orderedList', 'redo', 'strike', 'undo', 'blockquote'])
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Image & Médias')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->directory('blog/covers')
                        ->disk(config('filesystems.default', 'public'))
                        ->maxSize(2048)
                        ->imageEditor()
                        ->helperText('Format recommandé : 1200×630px (ratio 1.91:1)'),

                    TagsInput::make('tags')
                        ->label('Tags')
                        ->placeholder('Ajouter un tag et appuyer Entrée')
                        ->suggestions(['Bénin', 'Cotonou', 'Ouidah', 'Ganvié', 'Vaudou', 'Gastronomie', 'Nature', 'Culture', 'Voyage', 'Afrique']),
                ])
                ->columns(2),

            Section::make('Publication')
                ->icon('heroicon-o-calendar')
                ->schema([
                    Select::make('status')
                        ->options(['draft' => '⚫ Brouillon', 'published' => '🟢 Publié'])
                        ->default('draft')
                        ->required()
                        ->native(false),

                    DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->default(now())
                        ->helperText('Planifiez une publication future'),

                    TextInput::make('reading_time')
                        ->label('Temps de lecture (minutes)')
                        ->numeric()
                        ->default(5)
                        ->minValue(1)
                        ->helperText('Calculé automatiquement si vide'),
                ])
                ->columns(3),

            Section::make('SEO')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    TextInput::make('meta_title')
                        ->label('Titre SEO')
                        ->maxLength(60)
                        ->helperText('60 caractères max — laissez vide pour utiliser le titre'),

                    TextInput::make('meta_description')
                        ->label('Description SEO')
                        ->maxLength(160)
                        ->helperText('160 caractères max — laissez vide pour utiliser l\'extrait'),
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
                    ->label('')
                    ->disk(config('filesystems.default', 'public'))
                    ->size(48)
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45)
                    ->description(fn (BlogPost $r) => $r->category_label . ' · ' . $r->reading_time . ' min'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn ($state) => $state === 'published' ? 'Publié' : 'Brouillon')
                    ->colors(['success' => 'published', 'gray' => 'draft']),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié le')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Brouillon', 'published' => 'Publié']),
                Tables\Filters\SelectFilter::make('category')
                    ->options(['destinations' => 'Destinations', 'conseils' => 'Conseils', 'culture' => 'Culture', 'pratique' => 'Pratique']),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}