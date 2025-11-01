<?php

namespace App\Filament\Resources\WhatsAppGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WhatsAppGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Group Name')->required()->maxLength(255),
                TextInput::make('group_id')->label('Group ID')->required()->maxLength(255),
                TextInput::make('description')->label('Description')->maxLength(500),
            ]);
    }
}
