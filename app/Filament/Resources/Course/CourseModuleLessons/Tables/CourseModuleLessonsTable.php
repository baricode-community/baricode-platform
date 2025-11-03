<?php

namespace App\Filament\Resources\Course\CourseModuleLessons\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Course\CourseModule;

class CourseModuleLessonsTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseModule.course.courseCategory.name')
                    ->label('Kategori')
                    ->placeholder('Tanpa Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('courseModule.course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('courseModule.name')
                    ->label('Modul')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Urutan')
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
                SelectFilter::make('module_id')
                    ->label('Modul')
                    ->options(CourseModule::with('course.courseCategory')->get()->mapWithKeys(function ($module) {
                        $categoryName = $module->course->courseCategory ? $module->course->courseCategory->name : 'Tanpa Kategori';
                        return [$module->id => $categoryName . ' - ' . $module->course->title . ' - ' . $module->name];
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