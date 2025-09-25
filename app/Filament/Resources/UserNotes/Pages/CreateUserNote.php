<?php

namespace App\Filament\Resources\UserNotes\Pages;

use App\Filament\Resources\UserNotes\UserNoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserNote extends CreateRecord
{
    protected static string $resource = UserNoteResource::class;
}
