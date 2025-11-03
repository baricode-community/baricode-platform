<?php

namespace App\Filament\Resources\Course\CourseModuleLessons\Schemas;

use App\Models\Course\CourseModule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

class CourseModuleLessonForm
{
    public static function getSchema(): array
    {
        return [
            Select::make('module_id')
                ->label('Modul')
                ->options(CourseModule::with('course.courseCategory')->get()->mapWithKeys(function ($module) {
                    $categoryName = $module->course->courseCategory ? $module->course->courseCategory->name : 'Tanpa Kategori';
                    return [$module->id => $categoryName . ' - ' . $module->course->title . ' - ' . $module->name];
                }))
                ->searchable()
                ->required(),

            TextInput::make('title')
                ->label('Judul Pelajaran')
                ->required()
                ->maxLength(255),

            RichEditor::make('content')
                ->label('Konten')
                ->maxLength(65535)
                ->columnSpanFull(),

            TextInput::make('order')
                ->label('Urutan')
                ->numeric()
                ->required()
                ->default(1)
                ->minValue(1),
        ];
    }
}