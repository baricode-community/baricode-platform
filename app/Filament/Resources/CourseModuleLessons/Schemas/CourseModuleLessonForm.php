<?php

namespace App\Filament\Resources\CourseModuleLessons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class CourseModuleLessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('module_id')
                    ->label('Modul')
                    ->relationship('courseModule', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                
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
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
            ]);
    }
}
