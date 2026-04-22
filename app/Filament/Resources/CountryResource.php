<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $modelLabel       = 'Pays';
    protected static ?string $pluralModelLabel = 'Pays';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-globe-alt';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pays';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Destinations';
    }

    public static function form(Schema $schema): Schema
    {
        $disk = config('filesystems.default', 'public');

        return $schema->components([

            Section::make('Informations principales')
                ->icon('heroicon-o-globe-alt')
                ->schema([
                    TextInput::make('name')
                        ->label('Nom du pays')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state))),

                    TextInput::make('slug')
                        ->label('Slug URL')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Ex: togo, ghana, senegal'),

                    TextInput::make('code')
                        ->label('Code ISO (2 lettres)')
                        ->required()
                        ->maxLength(2)
                        ->placeholder('BJ')
                        ->helperText('BJ = Bénin, TG = Togo, GH = Ghana…'),

                    TextInput::make('flag_emoji')
                        ->label('Drapeau emoji')
                        ->placeholder('🇧🇯')
                        ->helperText('Copiez l\'emoji drapeau du pays'),

                    TextInput::make('capital')
                        ->label('Capitale')
                        ->placeholder('Cotonou'),

                    Select::make('continent')
                        ->label('Continent')
                        ->options([
                            'Afrique'   => 'Afrique',
                            'Europe'    => 'Europe',
                            'Asie'      => 'Asie',
                            'Amériques' => 'Amériques',
                            'Océanie'   => 'Océanie',
                        ])
                        ->default('Afrique'),
                ])
                ->columns(3),

            Section::make('Devise & Langue')
                ->schema([
                    TextInput::make('currency_code')
                        ->label('Code devise')
                        ->placeholder('XOF'),

                    TextInput::make('currency_name')
                        ->label('Nom devise')
                        ->placeholder('Franc CFA'),

                    TextInput::make('language')
                        ->label('Langue principale')
                        ->placeholder('Français'),

                    TextInput::make('population')
                        ->label('Population')
                        ->placeholder('13 millions'),

                    TextInput::make('area')
                        ->label('Superficie')
                        ->placeholder('114 763 km²'),
                ])
                ->columns(3),

            Section::make('Contenu éditorial')
                ->schema([
                    Textarea::make('description')
                        ->label('Description courte')
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText('Affiché dans la card pays et en intro de la page.'),

                    Textarea::make('history')
                        ->label('Histoire')
                        ->rows(4),

                    Textarea::make('culture')
                        ->label('Culture & traditions')
                        ->rows(4),

                    Textarea::make('practical_info')
                        ->label('Infos pratiques (visa, santé, sécurité…)')
                        ->rows(4),
                ]),

            Section::make('SEO')
                ->schema([
                    TextInput::make('meta_title')
                        ->label('Titre SEO')
                        ->maxLength(70),

                    TextInput::make('meta_description')
                        ->label('Description SEO')
                        ->maxLength(160),
                ])
                ->columns(2),

            Section::make('Image de couverture')
                ->schema([
                    FileUpload::make('cover_image')
                        ->label('Image principale')
                        ->image()
                        ->directory('countries')
                        ->disk($disk)
                        ->maxSize(3072)
                        ->imageEditor(),
                ]),

            Section::make('Paramètres')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Pays actif (visible sur le site)')
                        ->default(true),

                    Toggle::make('is_featured')
                        ->label('Mettre en avant'),

                    TextInput::make('featured_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('flag_emoji')
                    ->label('')
                    ->width(40),

                TextColumn::make('name')
                    ->label('Pays')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->badge(),

                TextColumn::make('capital')
                    ->label('Capitale'),

                TextColumn::make('cities_count')
                    ->label('Villes')
                    ->counts('cities')
                    ->badge()
                    ->color('success'),

                TextColumn::make('continent')
                    ->label('Continent')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
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
            ->defaultSort('featured_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit'   => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}