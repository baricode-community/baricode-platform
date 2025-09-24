<?php

namespace App\Filament\Resources\CourseCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Title')->required()->maxLength(255),
                Select::make('level')
                    ->label('Level')
                    ->options([
                        'pemula' => 'Pemula',
                        'menengah' => 'Menengah',
                        'lanjut' => 'Lanjut',
                    ])
                    ->required()
                    ->searchable(),
                TextInput::make('description')->label('Description')->required()->maxLength(1000),
            ]);
    }
}
