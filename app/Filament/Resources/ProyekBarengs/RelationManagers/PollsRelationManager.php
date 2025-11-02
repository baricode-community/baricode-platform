<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PollsRelationManager extends RelationManager
{
    protected static string $relationship = 'polls';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Polls';

    protected static ?string $modelLabel = 'Poll';

    protected static ?string $pluralModelLabel = 'Polls';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Poll untuk Proyek')
                    ->maxLength(255)
                    ->placeholder('Judul khusus untuk poll ini dalam konteks proyek')
                    ->helperText('Judul yang akan ditampilkan khusus untuk proyek ini'),
                    
                Textarea::make('description')
                    ->label('Deskripsi Relasi Poll')
                    ->rows(3)
                    ->placeholder('Jelaskan bagaimana poll ini terkait dengan proyek')
                    ->helperText('Deskripsi tentang hubungan poll ini dengan proyek'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Poll')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('Deskripsi Poll')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('pivot.title')
                    ->label('Judul dalam Proyek')
                    ->limit(50)
                    ->placeholder('Menggunakan judul asli'),
                    
                TextColumn::make('pivot.description')
                    ->label('Hubungan dengan Proyek')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
                    
                TextColumn::make('pivot.created_at')
                    ->label('Ditambahkan ke Proyek')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Poll')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('title')
                            ->label('Judul Poll untuk Proyek')
                            ->maxLength(255)
                            ->placeholder('Judul khusus untuk poll ini dalam konteks proyek')
                            ->helperText('Judul yang akan ditampilkan khusus untuk proyek ini'),
                        Textarea::make('description')
                            ->label('Deskripsi Relasi Poll')
                            ->rows(3)
                            ->placeholder('Jelaskan bagaimana poll ini terkait dengan proyek')
                            ->helperText('Deskripsi tentang hubungan poll ini dengan proyek'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        TextInput::make('title')
                            ->label('Judul Poll untuk Proyek')
                            ->maxLength(255)
                            ->placeholder('Judul khusus untuk poll ini dalam konteks proyek')
                            ->helperText('Judul yang akan ditampilkan khusus untuk proyek ini'),
                        Textarea::make('description')
                            ->label('Deskripsi Relasi Poll')
                            ->rows(3)
                            ->placeholder('Jelaskan bagaimana poll ini terkait dengan proyek')
                            ->helperText('Deskripsi tentang hubungan poll ini dengan proyek'),
                    ]),
                DetachAction::make()
                    ->label('Hapus dari Proyek'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Hapus dari Proyek'),
                ]),
            ])
            ->defaultSort('pivot.created_at', 'desc');
    }
}