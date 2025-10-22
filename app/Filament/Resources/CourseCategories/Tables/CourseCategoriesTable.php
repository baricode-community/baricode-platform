<?php

namespace App\Filament\Resources\CourseCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CourseCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->colors([
                        'success' => 'pemula',
                        'warning' => 'menengah',
                        'danger' => 'lanjut',
                    ])
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('courses_count')
                    ->label('Jumlah Kursus')
                    ->counts('courses')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Level')
                    ->options([
                        'pemula' => 'Pemula',
                        'menengah' => 'Menengah',
                        'lanjut' => 'Lanjut',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
