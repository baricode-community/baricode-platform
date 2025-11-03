<?php

namespace App\Filament\Resources\Course\CourseCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class CourseCategoryForm
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->maxLength(255),

            Select::make('level')
                ->label('Level')
                ->options([
                    'pemula' => 'Pemula',
                    'menengah' => 'Menengah',
                    'lanjut' => 'Lanjut',
                ])
                ->default('pemula')
                ->required(),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3)
                ->maxLength(1000),
        ];
    }
}