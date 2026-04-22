<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Témoignages';
    
    protected static ?string $modelLabel = 'Témoignage';
    
    protected static ?string $pluralModelLabel = 'Témoignages';
    
    protected static ?string $navigationGroup = 'Contenu';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du client')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nom du client')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('client_title')
                            ->label('Titre/Fonction')
                            ->placeholder('Ex: Voyageur de Paris, Photographe...')
                            ->maxLength(255),
                        
                        Forms\Components\FileUpload::make('client_photo')
                            ->label('Photo du client')
                            ->image()
                            ->directory('testimonials')
                            ->visibility('public')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->helperText('Photo optionnelle. Si vide, les initiales seront affichées'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Témoignage')
                    ->schema([
                        Forms\Components\Textarea::make('testimonial')
                            ->label('Témoignage')
                            ->required()
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Maximum 500 caractères'),
                        
                        Forms\Components\Select::make('rating')
                            ->label('Note')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (5 étoiles)',
                                4 => '⭐⭐⭐⭐ (4 étoiles)',
                                3 => '⭐⭐⭐ (3 étoiles)',
                                2 => '⭐⭐ (2 étoiles)',
                                1 => '⭐ (1 étoile)',
                            ])
                            ->required()
                            ->default(5),
                        
                        Forms\Components\TextInput::make('offer_title')
                            ->label('Offre concernée')
                            ->placeholder('Ex: Visite d\'Ouidah')
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('travel_date')
                            ->label('Date du voyage')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Publication')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(true)
                            ->helperText('Le témoignage sera visible sur le site'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Mis en avant')
                            ->default(false)
                            ->helperText('Sera affiché en priorité sur la page d\'accueil'),
                        
                        Forms\Components\TextInput::make('order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0)
                            ->helperText('0 = premier, les plus petits s\'affichent en premier'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client_photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->client_name) . '&color=B87516&background=FDF8F0'),
                
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('client_title')
                    ->label('Fonction')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('testimonial')
                    ->label('Témoignage')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('offer_title')
                    ->label('Offre')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Mis en avant')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('travel_date')
                    ->label('Date voyage')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publié')
                    ->placeholder('Tous')
                    ->trueLabel('Publiés uniquement')
                    ->falseLabel('Non publiés uniquement'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mis en avant')
                    ->placeholder('Tous')
                    ->trueLabel('Mis en avant uniquement')
                    ->falseLabel('Non mis en avant uniquement'),
                
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Note')
                    ->options([
                        5 => '5 étoiles',
                        4 => '4 étoiles',
                        3 => '3 étoiles',
                        2 => '2 étoiles',
                        1 => '1 étoile',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('toggle_published')
                    ->label(fn ($record) => $record->is_published ? 'Dépublier' : 'Publier')
                    ->icon(fn ($record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->is_published ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_published' => !$record->is_published]))
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_published ? 'Dépublier le témoignage ?' : 'Publier le témoignage ?')
                    ->modalDescription(fn ($record) => $record->is_published 
                        ? 'Le témoignage ne sera plus visible sur le site.' 
                        : 'Le témoignage sera visible sur le site.'),
                
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn ($record) => $record->is_featured ? 'Retirer de la mise en avant' : 'Mettre en avant')
                    ->icon(fn ($record) => $record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->color(fn ($record) => $record->is_featured ? 'warning' : 'gray')
                    ->action(fn ($record) => $record->update(['is_featured' => !$record->is_featured]))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publier')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Dépublier')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Mettre en avant')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'view' => Pages\ViewTestimonial::route('/{record}'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_published', true)->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}