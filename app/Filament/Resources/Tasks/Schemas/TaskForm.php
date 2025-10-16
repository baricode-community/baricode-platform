<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Tugas')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Tugas')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        RichEditor::make('content')
                            ->label('Konten/Detail Tugas')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                        
                        RichEditor::make('instructions')
                            ->label('Instruksi Pengerjaan')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Pengaturan')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya tugas aktif yang bisa dikerjakan'),
                        
                        TextInput::make('max_submissions_per_user')
                            ->label('Maksimal Submit per User')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText('Berapa kali seorang user bisa submit tugas ini'),
                        
                        FileUpload::make('attachments')
                            ->label('Lampiran Tugas')
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(10240)
                            ->directory('task-attachments')
                            ->columnSpanFull()
                            ->helperText('File panduan atau referensi untuk tugas ini'),
                    ])
                    ->columns(2),
            ]);
    }
}
