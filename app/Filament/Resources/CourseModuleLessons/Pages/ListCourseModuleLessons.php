<?php

namespace App\Filament\Resources\CourseModuleLessons\Pages;

use App\Filament\Resources\CourseModuleLessons\CourseModuleLessonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseModuleLessons extends ListRecords
{
    protected static string $resource = CourseModuleLessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
