<?php

namespace App\Filament\Resources\TaskSubmissions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;


class TaskSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Submission')
                    ->schema([
                        Select::make('task_id')
                            ->label('Task')
                            ->relationship('task', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($record) => $record !== null),
                        
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($record) => $record !== null),
                        
                        Select::make('assignment_id')
                            ->label('Assignment')
                            ->relationship('assignment', 'id')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Konten Submission')
                    ->schema([
                        RichEditor::make('submission_content')
                            ->label('Isi Submission')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'codeBlock',
                            ]),
                        
                        FileUpload::make('files')
                            ->label('File Lampiran')
                            ->multiple()
                            ->maxFiles(10)
                            ->maxSize(20480)
                            ->directory('task-submissions')
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Review')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Review',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'revision_requested' => 'Perlu Revisi',
                            ])
                            ->required()
                            ->default('pending'),
                        
                        TextInput::make('score')
                            ->label('Skor')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('Opsional: Berikan skor 0-100'),
                        
                        Textarea::make('review_notes')
                            ->label('Catatan Review')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Berikan feedback untuk user'),
                        
                        Placeholder::make('reviewed_info')
                            ->label('Info Review')
                            ->content(function ($record) {
                                if (!$record || !$record->reviewed_by) {
                                    return 'Belum direview';
                                }
                                return "Direview oleh: {$record->reviewer->name} pada " . 
                                       $record->reviewed_at->format('d M Y H:i');
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
