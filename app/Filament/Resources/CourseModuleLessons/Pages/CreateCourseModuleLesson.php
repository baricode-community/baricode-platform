<?php

namespace App\Filament\Resources\CourseModuleLessons\Pages;

use App\Filament\Resources\CourseModuleLessons\CourseModuleLessonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseModuleLesson extends CreateRecord
{
    protected static string $resource = CourseModuleLessonResource::class;
}
