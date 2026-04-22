<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Paramètres';
    
    protected static ?string $modelLabel = 'Paramètre';
    
    protected static ?string $pluralModelLabel = 'Paramètres';
    
    protected static ?string $navigationGroup = 'Configuration';
    
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Clé')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText('Identifiant unique du paramètre'),
                        
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'text' => 'Texte',
                                'image' => 'Image',
                                'json' => 'JSON',
                            ])
                            ->required()
                            ->reactive()
                            ->default('text'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Valeur')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Valeur')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'text'),
                        
                        Forms\Components\FileUpload::make('value')
                            ->label('Image')
                            ->image()
                            ->directory('settings')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                null,
                            ])
                            ->maxSize(5120)
                            ->helperText('Formats: JPG, PNG, WebP. Max: 5MB. Recommandé: 1920x1080px')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image'),
                        
                        Forms\Components\Textarea::make('value')
                            ->label('JSON')
                            ->rows(5)
                            ->helperText('Format JSON valide')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Clé')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'image' => 'success',
                        'json' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\ImageColumn::make('value')
                    ->label('Aperçu')
                    ->circular()
                    ->visible(fn ($record) => $record->type === 'image'),
                
                Tables\Columns\TextColumn::make('value')
                    ->label('Valeur')
                    ->limit(50)
                    ->visible(fn ($record) => $record->type === 'text'),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'text' => 'Texte',
                        'image' => 'Image',
                        'json' => 'JSON',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return true;
    }
}