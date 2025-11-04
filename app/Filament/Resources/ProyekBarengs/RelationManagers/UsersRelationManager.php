<?php

namespace App\Filament\Resources\ProyekBarengs\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Colors\Color;

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
                    
                Toggle::make('is_approved')
                    ->label('Status Persetujuan')
                    ->helperText('Aktifkan untuk menyetujui anggota ini dalam proyek')
                    ->default(false),
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
                    
                IconColumn::make('pivot.is_approved')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor(Color::Green)
                    ->falseColor(Color::Red)
                    ->tooltip(fn ($state): string => $state ? 'Disetujui' : 'Menunggu Persetujuan')
                    ->sortable(),
                    
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
                        Toggle::make('is_approved')
                            ->label('Langsung Setujui')
                            ->helperText('Aktifkan untuk langsung menyetujui anggota ini')
                            ->default(false),
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->visible(fn ($record): bool => !$record->pivot->is_approved)
                    ->action(function ($record) {
                        $record->pivot->update(['is_approved' => true]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Anggota')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui anggota ini untuk bergabung dalam proyek?'),
                    
                Action::make('reject')
                    ->label('Batalkan Persetujuan')
                    ->icon('heroicon-o-x-circle')
                    ->color(Color::Red)
                    ->visible(fn ($record): bool => $record->pivot->is_approved)
                    ->action(function ($record) {
                        $record->pivot->update(['is_approved' => false]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Persetujuan')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan persetujuan anggota ini?'),
                    
                EditAction::make()
                    ->form([
                        Textarea::make('description')
                            ->label('Posisi & Deskripsi Peran')
                            ->rows(5)
                            ->placeholder("Contoh:\nPosisi: Project Manager\nPeran: Mengkoordinasi tim, mengatur timeline, dan memastikan deliverable tercapai\n\nTanggung jawab:\n- Mengelola komunikasi antar tim\n- Monitoring progress proyek\n- Risk management")
                            ->helperText('Jelaskan posisi, peran, dan tanggung jawab spesifik anggota dalam proyek ini'),
                        Toggle::make('is_approved')
                            ->label('Status Persetujuan')
                            ->helperText('Aktifkan untuk menyetujui anggota ini dalam proyek'),
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