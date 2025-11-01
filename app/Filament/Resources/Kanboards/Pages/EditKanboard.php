<?php

namespace App\Filament\Resources\Kanboards\Pages;

use App\Filament\Resources\Kanboards\KanboardResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKanboard extends EditRecord
{
    protected static string $resource = KanboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
