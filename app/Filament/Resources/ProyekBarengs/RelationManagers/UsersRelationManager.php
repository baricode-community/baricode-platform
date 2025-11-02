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

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Anggota Tim';

    protected static ?string $modelLabel = 'Anggota';

    protected static ?string $pluralModelLabel = 'Anggota Tim';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                    
                Textarea::make('description')
                    ->label('Deskripsi Peran')
                    ->rows(3)
                    ->placeholder('Jelaskan peran anggota dalam proyek ini')
                    ->helperText('Deskripsi tentang peran atau tanggung jawab anggota dalam proyek ini'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('pivot.description')
                    ->label('Peran dalam Proyek')
                    ->limit(50)
                    ->placeholder('Tidak ada deskripsi'),
                    
                TextColumn::make('pivot.created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Anggota')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Textarea::make('description')
                            ->label('Deskripsi Peran')
                            ->rows(3)
                            ->placeholder('Jelaskan peran anggota dalam proyek ini')
                            ->helperText('Deskripsi tentang peran atau tanggung jawab anggota dalam proyek ini'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Textarea::make('description')
                            ->label('Deskripsi Peran')
                            ->rows(3)
                            ->placeholder('Jelaskan peran anggota dalam proyek ini')
                            ->helperText('Deskripsi tentang peran atau tanggung jawab anggota dalam proyek ini'),
                    ]),
                DetachAction::make()
                    ->label('Hapus dari Tim'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Hapus dari Tim'),
                ]),
            ]);
    }
}