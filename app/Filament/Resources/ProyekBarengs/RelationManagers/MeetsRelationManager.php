<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class MeetsRelationManager extends RelationManager
{
    protected static string $relationship = 'meets';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Meetings';

    protected static ?string $modelLabel = 'Meeting';

    protected static ?string $pluralModelLabel = 'Meetings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('description')
                    ->label('Deskripsi Relasi Meeting')
                    ->rows(3)
                    ->placeholder('Jelaskan bagaimana meeting ini terkait dengan proyek')
                    ->helperText('Deskripsi tentang hubungan meeting ini dengan proyek'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Meeting')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('Deskripsi Meeting')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum dijadwalkan'),
                    
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'scheduled',
                        'primary' => 'ongoing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),
                    
                TextColumn::make('pivot.description')
                    ->label('Hubungan dengan Proyek')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('pivot.created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Meeting')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Textarea::make('description')
                            ->label('Deskripsi Relasi Meeting')
                            ->rows(3)
                            ->placeholder('Jelaskan bagaimana meeting ini terkait dengan proyek')
                            ->helperText('Deskripsi tentang hubungan meeting ini dengan proyek'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Textarea::make('description')
                            ->label('Deskripsi Relasi Meeting')
                            ->rows(3)
                            ->placeholder('Jelaskan bagaimana meeting ini terkait dengan proyek')
                            ->helperText('Deskripsi tentang hubungan meeting ini dengan proyek'),
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
            ->defaultSort('scheduled_at', 'desc');
    }
}