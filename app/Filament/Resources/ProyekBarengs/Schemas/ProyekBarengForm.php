<?php

namespace App\Filament\Resources\ProyekBarengs\Schemas;

use App\Models\User\User;
use App\Models\Meet;
use App\Models\Kanboard;
use App\Models\Poll;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class ProyekBarengForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Proyek')
                    ->description('Informasi dasar tentang proyek kolaboratif')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Proyek')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                            
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Toggle::make('is_finished')
                            ->label('Proyek Selesai')
                            ->default(false),
                    ])
                    ->columns(2),
                    
                \Filament\Schemas\Components\Section::make('Tim & Kolaborator')
                    ->description('Anggota tim yang terlibat dalam proyek dengan detail posisi dan peran')
                    ->schema([
                        Select::make('users')
                            ->label('Anggota Tim')
                            ->relationship('users', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih anggota tim')
                            ->helperText('Pilih pengguna yang akan terlibat dalam proyek ini. Detail posisi dan peran dapat diatur di tab "Anggota Tim" setelah menyimpan.'),
                    ]),
                    
                \Filament\Schemas\Components\Section::make('Meetings')
                    ->description('Meeting yang terkait dengan proyek')
                    ->schema([
                        Select::make('meets')
                            ->label('Meetings')
                            ->relationship('meets', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih meetings')
                            ->helperText('Pilih meeting yang terkait dengan proyek ini'),
                    ]),
                    
                \Filament\Schemas\Components\Section::make('Kanboards')
                    ->description('Kanboard yang digunakan untuk manajemen proyek')
                    ->schema([
                        Select::make('kanboards')
                            ->label('Kanboards')
                            ->relationship('kanboards', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih kanboards')
                            ->helperText('Pilih kanboard yang akan digunakan dalam proyek ini'),
                    ]),
                    
                \Filament\Schemas\Components\Section::make('External Tools & Links')
                    ->description('Link ke tools eksternal untuk manajemen proyek')
                    ->schema([
                        Repeater::make('kanboardLinks')
                            ->label('External Kanboard Links')
                            ->relationship('kanboardLinks')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Nama Tool')
                                    ->required()
                                    ->maxLength(255),
                                    
                                TextInput::make('link')
                                    ->label('URL Link')
                                    ->required()
                                    ->url()
                                    ->placeholder('https://example.com'),
                                    
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah External Tool')
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->columns(2),
                    ]),
                    
                \Filament\Schemas\Components\Section::make('Polls')
                    ->description('Polls yang terkait dengan proyek')
                    ->schema([
                        Select::make('polls')
                            ->label('Polls')
                            ->relationship('polls', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih polls')
                            ->helperText('Pilih polls yang terkait dengan proyek ini'),
                    ]),
            ]);
    }
}
