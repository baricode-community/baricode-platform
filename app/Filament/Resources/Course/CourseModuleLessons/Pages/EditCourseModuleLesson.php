<?php

namespace App\Filament\Resources\Course\CourseModuleLessons\Pages;

use App\Filament\Resources\Course\CourseModuleLessons\CourseModuleLessonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseModuleLesson extends EditRecord
{
    protected static string $resource = CourseModuleLessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}