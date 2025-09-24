<?php

namespace App\Filament\Resources\LessonDetails\Pages;

use App\Filament\Resources\LessonDetails\LessonDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLessonDetails extends ListRecords
{
    protected static string $resource = LessonDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
