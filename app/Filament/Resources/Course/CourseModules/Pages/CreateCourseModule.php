<?php

namespace App\Filament\Resources\Course\CourseModules\Pages;

use App\Filament\Resources\Course\CourseModules\CourseModuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseModule extends CreateRecord
{
    protected static string $resource = CourseModuleResource::class;
}