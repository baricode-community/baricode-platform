<?php

namespace App\Filament\Resources\Course\CourseModules\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Learning\Course;

class CourseModulesTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.courseCategory.name')
                    ->label('Kategori')
                    ->placeholder('Tanpa Kategori')
                    ->searchable()
                    ->sortable(),

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
                    ->sortable(),

                TextColumn::make('course_module_lessons_count')
                    ->label('Jumlah Pelajaran')
                    ->counts('courseModuleLessons')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Kursus')
                    ->options(Course::with('courseCategory')->get()->mapWithKeys(function ($course) {
                        $categoryName = $course->courseCategory ? $course->courseCategory->name : 'Tanpa Kategori';
                        return [$course->id => $categoryName . ' - ' . $course->title];
                    })),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}