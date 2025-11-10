<?php

namespace App\Filament\Resources\Course\Courses\Schemas;

use App\Models\Learning\CourseCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

class CourseForm
{
    public static function getSchema(): array
    {
        return [
            Select::make('category_id')
                ->label('Kategori')
                ->options(CourseCategory::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('title')
                ->label('Judul Kursus')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique('courses', 'slug', ignoreRecord: true),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3)
                ->maxLength(1000),

            FileUpload::make('thumbnail')
                ->label('Thumbnail')
                ->image()
                ->directory('course-thumbnails')
                ->imagePreviewHeight('100'),

            Toggle::make('is_published')
                ->label('Dipublikasikan')
                ->default(false),
        ];
    }
}