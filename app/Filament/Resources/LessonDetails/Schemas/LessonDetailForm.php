<?php

namespace App\Filament\Resources\LessonDetails\Schemas;

use Filament\Forms\Components\RichEditor;
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
            ]);
    }
}
