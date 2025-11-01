<?php

namespace App\Filament\Resources\WhatsAppGroups\Pages;

use App\Filament\Resources\WhatsAppGroups\WhatsAppGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppGroups extends ListRecords
{
    protected static string $resource = WhatsAppGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
