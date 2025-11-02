<?php

namespace App\Filament\Resources\ProyekBarengs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class ProyekBarengsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('ID berhasil disalin')
                    ->width('80px'),
                    
                TextColumn::make('title')
                    ->label('Judul Proyek')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                    
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(100)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 100) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                    
                BadgeColumn::make('is_finished')
                    ->label('Status')
                    ->getStateUsing(fn ($record): string => $record->is_finished ? 'Selesai' : 'Berlangsung')
                    ->colors([
                        'success' => 'Selesai',
                        'warning' => 'Berlangsung',
                    ]),
                    
                TextColumn::make('users_count')
                    ->label('Tim')
                    ->counts('users')
                    ->suffix(' orang')
                    ->sortable(),
                    
                TextColumn::make('meets_count')
                    ->label('Meetings')
                    ->counts('meets')
                    ->suffix(' meeting')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('kanboards_count')
                    ->label('Kanboards')
                    ->counts('kanboards')
                    ->suffix(' board')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('kanboard_links_count')
                    ->label('External Tools')
                    ->counts('kanboardLinks')
                    ->suffix(' tool')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('polls_count')
                    ->label('Polls')
                    ->counts('polls')
                    ->suffix(' poll')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_finished')
                    ->label('Status Proyek')
                    ->placeholder('Semua proyek')
                    ->trueLabel('Selesai')
                    ->falseLabel('Berlangsung'),
                    
                Filter::make('has_team')
                    ->label('Memiliki Tim')
                    ->query(fn (Builder $query): Builder => $query->has('users'))
                    ->toggle(),
                    
                Filter::make('has_meetings')
                    ->label('Memiliki Meetings')
                    ->query(fn (Builder $query): Builder => $query->has('meets'))
                    ->toggle(),
                    
                Filter::make('has_kanboards')
                    ->label('Memiliki Kanboards')
                    ->query(fn (Builder $query): Builder => $query->has('kanboards'))
                    ->toggle(),
                    
                Filter::make('has_polls')
                    ->label('Memiliki Polls')
                    ->query(fn (Builder $query): Builder => $query->has('polls'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('30s');
    }
}
