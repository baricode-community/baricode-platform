<?php

namespace App\Filament\Resources\ProyekBarengs\Pages;

use App\Filament\Resources\ProyekBarengs\ProyekBarengResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProyekBareng extends EditRecord
{
    protected static string $resource = ProyekBarengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
