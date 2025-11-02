<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class KanboardLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'kanboardLinks';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'External Tools & Links';

    protected static ?string $modelLabel = 'External Tool';

    protected static ?string $pluralModelLabel = 'External Tools';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama Tool')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Misal: Trello Board, Figma Design, dll'),
                    
                TextInput::make('link')
                    ->label('URL Link')
                    ->required()
                    ->url()
                    ->placeholder('https://example.com')
                    ->helperText('Link ke tool eksternal yang digunakan dalam proyek'),
                    
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Jelaskan tujuan atau penggunaan tool ini dalam proyek')
                    ->helperText('Deskripsi tentang bagaimana tool ini digunakan dalam proyek'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Nama Tool')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('link')
                    ->label('Link')
                    ->limit(50)
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->iconPosition('after'),
                    
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(100)
                    ->placeholder('Tidak ada deskripsi')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 100) {
                            return null;
                        }
                        return $state;
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah External Tool'),
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