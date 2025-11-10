<?php

namespace App\Filament\Resources\Course\CourseModules\Schemas;

use App\Models\Learning\Course;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class CourseModuleForm
{
    public static function getSchema(): array
    {
        return [
            Select::make('course_id')
                ->label('Kursus')
                ->options(Course::with('courseCategory')->get()->mapWithKeys(function ($course) {
                    $categoryName = $course->courseCategory ? $course->courseCategory->name : 'Tanpa Kategori';
                    return [$course->id => $categoryName . ' - ' . $course->title];
                }))
                ->searchable()
                ->required(),

            TextInput::make('name')
                ->label('Nama Modul')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3)
                ->maxLength(1000),

            TextInput::make('order')
                ->label('Urutan')
                ->numeric()
                ->required()
                ->default(1)
                ->minValue(1),
        ];
    }
}