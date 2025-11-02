<?php

namespace App\Filament\Resources\ProyekBarengs\Pages;

use App\Filament\Resources\ProyekBarengs\ProyekBarengResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProyekBarengs extends ListRecords
{
    protected static string $resource = ProyekBarengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
