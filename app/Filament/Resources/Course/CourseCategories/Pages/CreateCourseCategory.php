<?php

namespace App\Filament\Resources\Course\CourseCategories\Pages;

use App\Filament\Resources\Course\CourseCategories\CourseCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseCategory extends CreateRecord
{
    protected static string $resource = CourseCategoryResource::class;
}