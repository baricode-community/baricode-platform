<?php

namespace App\Filament\Resources\UserNotes\Pages;

use App\Filament\Resources\UserNotes\UserNoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserNote extends EditRecord
{
    protected static string $resource = UserNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
