<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
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

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;
    protected static ?string $modelLabel = 'Membre';
    protected static ?string $pluralModelLabel = 'Équipe';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contenu';
    }

    public static function getNavigationSort(): int
    {
        return 10;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Identité')
                ->schema([
                    TextInput::make('name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('role')
                        ->label('Rôle / Poste')
                        ->placeholder('Ex: Co-fondateur & CEO, Responsable guides...')
                        ->required()
                        ->maxLength(100),

                    Textarea::make('bio')
                        ->label('Biographie courte')
                        ->helperText('2–3 phrases. Affiché sur la page À propos.')
                        ->rows(3)
                        ->maxLength(300)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Photo & Liens')
                ->schema([
                    FileUpload::make('photo')
                        ->label('Photo de profil')
                        ->helperText('Format carré recommandé : 400×400 px. JPEG ou WebP. Max 1 Mo.')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1'])
                        ->disk(config('filesystems.default', 'public'))
                        ->directory('team')
                        ->visibility('public')
                        ->maxSize(1024)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->columnSpanFull(),

                    TextInput::make('linkedin_url')
                        ->label('LinkedIn URL')
                        ->url()
                        ->placeholder('https://linkedin.com/in/prenom-nom')
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email (optionnel)')
                        ->email()
                        ->placeholder('prenom@discovtrip.com')
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Affichage')
                ->schema([
                    TextInput::make('display_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(99)
                        ->minValue(1)
                        ->helperText('1 = affiché en premier.'),

                    Toggle::make('is_active')
                        ->label('Visible sur le site')
                        ->default(true),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->size(48)
                    ->disk(config('filesystems.default', 'public'))
                    ->defaultImageUrl(fn($record) =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=B8751A&color=FDFAF6&bold=true'
                    ),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('display_order')
                    ->label('Ordre')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Visible'),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->actions([
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
            'index'  => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit'   => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}