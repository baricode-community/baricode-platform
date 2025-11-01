<?php

namespace App\Filament\Resources\WhatsAppGroups\Pages;

use App\Filament\Resources\WhatsAppGroups\WhatsAppGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppGroup extends EditRecord
{
    protected static string $resource = WhatsAppGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
