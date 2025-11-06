<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UsefulLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'usefulLinks';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Link Bermanfaat';

    protected static ?string $modelLabel = 'Link Bermanfaat';

    protected static ?string $pluralModelLabel = 'Link Bermanfaat';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Link')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Misal: GitHub Repository, API Documentation, dll'),
                    
                TextInput::make('link')
                    ->label('URL Link')
                    ->required()
                    ->url()
                    ->placeholder('https://example.com')
                    ->helperText('URL lengkap termasuk https://'),
                    
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Jelaskan manfaat dan kegunaan link ini dalam proyek')
                    ->helperText('Deskripsi tentang manfaat link ini untuk proyek'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(80)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('link')
                    ->label('URL')
                    ->limit(50)
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab()
                    ->copyable()
                    ->copyMessage('URL berhasil disalin'),
                    
                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Link Bermanfaat'),
            ])
            ->recordActions([
                Action::make('open_link')
                    ->label('Buka Link')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab()
                    ->color('primary'),
                EditAction::make(),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}