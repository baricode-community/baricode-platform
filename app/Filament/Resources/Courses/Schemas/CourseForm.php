<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use App\Models\Course\CourseCategory;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('courseCategory', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                FileUpload::make('thumbnail')
                    ->label('Thumbnail')
                    ->image()
                    ->directory('course-thumbnails')
                    ->columnSpanFull(),
                
                Toggle::make('is_published')
                    ->label('Dipublikasikan')
                    ->default(false),
            ]);
    }
}
