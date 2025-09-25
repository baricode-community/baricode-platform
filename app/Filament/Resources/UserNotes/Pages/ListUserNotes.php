<?php

namespace App\Filament\Resources\UserNotes\Pages;

use App\Filament\Resources\UserNotes\UserNoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserNotes extends ListRecords
{
    protected static string $resource = UserNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
