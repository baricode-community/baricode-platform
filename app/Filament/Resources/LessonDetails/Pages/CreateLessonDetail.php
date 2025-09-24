<?php

namespace App\Filament\Resources\LessonDetails\Pages;

use App\Filament\Resources\LessonDetails\LessonDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLessonDetail extends CreateRecord
{
    protected static string $resource = LessonDetailResource::class;
}
