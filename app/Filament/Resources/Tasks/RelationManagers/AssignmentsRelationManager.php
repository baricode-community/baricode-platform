<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Models\User;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_ids')
                    ->label('Users')
                    ->multiple()
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih satu atau lebih user. Jika pilih multiple, semua akan dapat assignment yang sama.'),
                
                TextInput::make('title')
                    ->label('Judul Assignment')
                    ->maxLength(255)
                    ->helperText('Opsional: Judul untuk membedakan assignment ini dari yang lain'),
                
                Textarea::make('description')
                    ->label('Deskripsi Assignment')
                    ->rows(3)
                    ->helperText('Opsional: Deskripsi khusus untuk assignment ini'),
                
                DateTimePicker::make('due_date')
                    ->label('Deadline')
                    ->helperText('Kapan tugas ini harus selesai? (Opsional)'),
                
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),
                
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->helperText('Catatan atau instruksi tambahan untuk user ini'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Assignment')
                    ->searchable()
                    ->placeholder('(No title)')
                    ->toggleable(),
                
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),
                
                TextColumn::make('assignedBy.name')
                    ->label('Assigned By')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('assigned_at')
                    ->label('Tanggal Assignment')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                
                TextColumn::make('due_date')
                    ->label('Deadline')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Tidak ada deadline'),
                
                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, $livewire) {
                        $task = $livewire->ownerRecord;
                        $userIds = is_array($data['user_ids']) ? $data['user_ids'] : [$data['user_ids']];
                        
                        $createdAssignments = [];
                        foreach ($userIds as $userId) {
                            $assignment = $task->assignments()->create([
                                'user_id' => $userId,
                                'title' => $data['title'] ?? null,
                                'description' => $data['description'] ?? null,
                                'due_date' => $data['due_date'] ?? null,
                                'status' => $data['status'] ?? 'pending',
                                'notes' => $data['notes'] ?? null,
                                'assigned_by' => auth()->id(),
                                'assigned_at' => now(),
                            ]);
                            $createdAssignments[] = $assignment;
                        }
                        
                        if (count($createdAssignments) > 1) {
                            Notification::make()
                                ->success()
                                ->title('Assignments Berhasil Dibuat')
                                ->body("Berhasil membuat " . count($createdAssignments) . " assignment(s)")
                                ->send();
                        }
                        
                        // Return first assignment untuk Filament
                        return $createdAssignments[0] ?? null;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->helperText('User tidak bisa diubah saat edit. Hapus dan buat assignment baru jika perlu ganti user.'),
                        
                        TextInput::make('title')
                            ->label('Judul Assignment')
                            ->maxLength(255)
                            ->helperText('Opsional: Judul untuk membedakan assignment ini dari yang lain'),
                        
                        Textarea::make('description')
                            ->label('Deskripsi Assignment')
                            ->rows(3)
                            ->helperText('Opsional: Deskripsi khusus untuk assignment ini'),
                        
                        DateTimePicker::make('due_date')
                            ->label('Deadline')
                            ->helperText('Kapan tugas ini harus selesai? (Opsional)'),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'Dalam Pengerjaan',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required(),
                        
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->helperText('Catatan atau instruksi tambahan untuk user ini'),
                    ]),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('assigned_at', 'desc');
    }
}
