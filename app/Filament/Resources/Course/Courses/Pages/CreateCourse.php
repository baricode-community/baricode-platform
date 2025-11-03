<?php

namespace App\Filament\Resources\Course\Courses\Pages;

use App\Filament\Resources\Course\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
}