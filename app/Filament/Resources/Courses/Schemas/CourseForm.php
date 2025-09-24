<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\CourseCategory;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Kursus')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Kategori Kursus')
                    ->options(CourseCategory::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Checkbox::make('is_published')
                    ->label('Apakah kursus ini dipublikasikan?')
                    ->default(false),
            ]);
    }
}
