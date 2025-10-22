<?php

namespace App\Filament\Resources\CourseModules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CourseModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Nama Modul')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('courseModuleLessons_count')
                    ->label('Pelajaran')
                    ->counts('courseModuleLessons')
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
                SelectFilter::make('course_id')
                    ->label('Kursus')
                    ->relationship('course', 'title'),
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
