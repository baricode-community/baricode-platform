<?php

namespace App\Filament\Resources\Course\CourseCategories\Pages;

use App\Filament\Resources\Course\CourseCategories\CourseCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseCategory extends EditRecord
{
    protected static string $resource = CourseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}