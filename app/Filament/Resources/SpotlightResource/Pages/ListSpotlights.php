<?php
namespace App\Filament\Resources\SpotlightResource\Pages;
use App\Filament\Resources\SpotlightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpotlights extends ListRecords {
    protected static string $resource = SpotlightResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}