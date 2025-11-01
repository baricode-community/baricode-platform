<?php

namespace App\Filament\Resources\Kanboards\Pages;

use App\Filament\Resources\Kanboards\KanboardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKanboards extends ListRecords
{
    protected static string $resource = KanboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
