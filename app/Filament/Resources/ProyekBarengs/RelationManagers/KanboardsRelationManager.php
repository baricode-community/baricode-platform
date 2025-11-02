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

class KanboardsRelationManager extends RelationManager
{
    protected static string $relationship = 'kanboards';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Kanboards';

    protected static ?string $modelLabel = 'Kanboard';

    protected static ?string $pluralModelLabel = 'Kanboards';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('description')
                    ->label('Deskripsi Penggunaan Kanboard')
                    ->rows(3)
                    ->placeholder('Jelaskan bagaimana kanboard ini digunakan dalam proyek')
                    ->helperText('Deskripsi tentang penggunaan kanboard ini dalam proyek'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Kanboard')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('Deskripsi Kanboard')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'archived',
                    ])
                    ->sortable(),
                    
                TextColumn::make('pivot.description')
                    ->label('Penggunaan dalam Proyek')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
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
                    ->label('Tambah Kanboard')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Textarea::make('description')
                            ->label('Deskripsi Penggunaan Kanboard')
                            ->rows(3)
                            ->placeholder('Jelaskan bagaimana kanboard ini digunakan dalam proyek')
                            ->helperText('Deskripsi tentang penggunaan kanboard ini dalam proyek'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Textarea::make('description')
                            ->label('Deskripsi Penggunaan Kanboard')
                            ->rows(3)
                            ->placeholder('Jelaskan bagaimana kanboard ini digunakan dalam proyek')
                            ->helperText('Deskripsi tentang penggunaan kanboard ini dalam proyek'),
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