<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UsefulLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'usefulLinks';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Link')
                    ->required()
                    ->maxLength(255),
                    
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Jelaskan manfaat dan kegunaaan link ini dalam proyek')
                    ->helperText('Deskripsi tentang manfaat link ini untuk proyek'),
                    
                TextInput::make('link')
                    ->label('URL Link')
                    ->required()
                    ->url()
                    ->placeholder('https://example.com')
                    ->helperText('URL lengkap termasuk https://')
                    ->columnSpanFull(),
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}