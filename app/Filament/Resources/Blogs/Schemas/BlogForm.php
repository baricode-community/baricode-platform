<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Blog')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                TextInput::make('excerpt')
                    ->label('Cuplikan')
                    ->maxLength(500)
                    ->nullable(),
                RichEditor::make('content')
                    ->label('Isi Blog')
                    ->maxLength(65535)
                    ->required()
                    ->columnSpanFull()
                    ->extraAttributes(['style' => 'min-height:400px;']),
            ]);
    }
}
