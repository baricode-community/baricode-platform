<?php

namespace App\Filament\Resources\Course\CourseModuleLessons\Pages;

use App\Filament\Resources\Course\CourseModuleLessons\CourseModuleLessonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseModuleLesson extends CreateRecord
{
    protected static string $resource = CourseModuleLessonResource::class;
}