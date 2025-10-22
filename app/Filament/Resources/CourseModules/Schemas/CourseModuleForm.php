<?php

namespace App\Filament\Resources\CourseModules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CourseModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label('Kursus')
                    ->relationship('course', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                TextInput::make('name')
                    ->label('Nama Modul')
                    ->required()
                    ->maxLength(255),
                
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
            ]);
    }
}
