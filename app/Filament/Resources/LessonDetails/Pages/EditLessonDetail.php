<?php

namespace App\Filament\Resources\LessonDetails\Pages;

use App\Filament\Resources\LessonDetails\LessonDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLessonDetail extends EditRecord
{
    protected static string $resource = LessonDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
