<?php

namespace App\Filament\Resources\Course\CourseModules\Pages;

use App\Filament\Resources\Course\CourseModules\CourseModuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseModule extends EditRecord
{
    protected static string $resource = CourseModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}