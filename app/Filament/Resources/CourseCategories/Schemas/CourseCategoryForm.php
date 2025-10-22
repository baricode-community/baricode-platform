<?php

namespace App\Filament\Resources\CourseCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CourseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->rows(4)
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
