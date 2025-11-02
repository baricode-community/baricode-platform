<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
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
                    ->maxLength(255)
                    ->disabled()
                    ->helperText('Nama pengguna tidak dapat diubah'),
                    
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->disabled()
                    ->helperText('Email pengguna tidak dapat diubah'),
                    
                Textarea::make('description')
                    ->label('Posisi & Deskripsi Peran')
                    ->rows(4)
                    ->placeholder("Contoh:\nPosisi: Project Manager\nPeran: Mengkoordinasi tim, mengatur timeline, dan memastikan deliverable tercapai\n\nTanggung jawab:\n- Mengelola komunikasi antar tim\n- Monitoring progress proyek\n- Risk management")
                    ->helperText('Jelaskan posisi, peran, dan tanggung jawab spesifik anggota dalam proyek ini')
                    ->columnSpanFull(),
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
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('pivot.description')
                    ->label('Posisi & Peran dalam Proyek')
                    ->limit(80)
                    ->placeholder('Belum ada deskripsi posisi')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 80) {
                            return null;
                        }
                        return $state;
                    })
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'Belum ada deskripsi posisi';
                        
                        // Extract position from description if it follows the format
                        $lines = explode("\n", $state);
                        $positionLine = '';
                        foreach ($lines as $line) {
                            if (str_starts_with(trim($line), 'Posisi:') || str_starts_with(trim($line), 'Position:')) {
                                $positionLine = trim(str_replace(['Posisi:', 'Position:'], '', $line));
                                break;
                            }
                        }
                        
                        if ($positionLine) {
                            return $positionLine . ' • ' . Str::limit($state, 50);
                        }
                        
                        return Str::limit($state, 80);
                    }),
                    
                TextColumn::make('pivot.created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Anggota Tim')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Textarea::make('description')
                            ->label('Posisi & Deskripsi Peran')
                            ->rows(5)
                            ->placeholder("Contoh:\nPosisi: Project Manager\nPeran: Mengkoordinasi tim, mengatur timeline, dan memastikan deliverable tercapai\n\nTanggung jawab:\n- Mengelola komunikasi antar tim\n- Monitoring progress proyek\n- Risk management")
                            ->helperText('Jelaskan posisi, peran, dan tanggung jawab spesifik anggota dalam proyek ini'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Textarea::make('description')
                            ->label('Posisi & Deskripsi Peran')
                            ->rows(5)
                            ->placeholder("Contoh:\nPosisi: Project Manager\nPeran: Mengkoordinasi tim, mengatur timeline, dan memastikan deliverable tercapai\n\nTanggung jawab:\n- Mengelola komunikasi antar tim\n- Monitoring progress proyek\n- Risk management")
                            ->helperText('Jelaskan posisi, peran, dan tanggung jawab spesifik anggota dalam proyek ini'),
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