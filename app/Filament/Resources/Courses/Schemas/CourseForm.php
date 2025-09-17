<?php

namespace App\Filament\Resources\Courses\Schemas;

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
                    ->label('Course Title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->label('Course Description')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Checkbox::make('is_published')
                    ->label('Published')
                    ->default(false),
            ]);
    }
}
