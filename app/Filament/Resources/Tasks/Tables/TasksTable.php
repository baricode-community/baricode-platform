<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                TextColumn::make('user.name')
                    ->label('Pembuat')
                    ->searchable()
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('assignments_count')
                    ->label('Jumlah Assignment')
                    ->counts('assignments')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('submissions_count')
                    ->label('Total Submission')
                    ->counts('submissions')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('pending_submissions_count')
                    ->label('Pending Review')
                    ->counts('pendingSubmissions')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                
                TextColumn::make('max_submissions_per_user')
                    ->label('Max Submit/User')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
                
                SelectFilter::make('user_id')
                    ->label('Pembuat')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
