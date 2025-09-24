<?php

namespace App\Filament\Resources\LessonDetails\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LessonDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('references')
                    ->label('Referensi Sumber Pembelajaran')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('description')->nullable(),
                        TextInput::make('link')->url()->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
