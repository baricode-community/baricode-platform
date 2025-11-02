<?php

namespace App\Filament\Resources\ProyekBarengs\Pages;

use App\Filament\Resources\ProyekBarengs\ProyekBarengResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProyekBareng extends ViewRecord
{
    protected static string $resource = ProyekBarengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}