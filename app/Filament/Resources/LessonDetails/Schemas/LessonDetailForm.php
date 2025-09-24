<?php

namespace App\Filament\Resources\LessonDetails\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LessonDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('module_id')
                    ->label('Modul Kursus')
                    ->relationship('courseModule', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->label('Judul Pembelajaran')
                    ->default('Judul Pembelajaran'),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('references')
                    ->label('Referensi Sumber Pembelajaran')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->default('Referensi belajar'),
                        TextInput::make('description')->nullable(),
                        TextInput::make('link')
                            ->required()
                            ->url()
                            ->default('https://youtube.com/@barizaloka'),
                    ])
                    ->columns(3),
            ]);
    }
}
