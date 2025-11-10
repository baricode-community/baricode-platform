<?php

namespace App\Filament\Resources\Course\Courses\RelationManagers;

use App\Models\Learning\CourseModule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class CourseModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'courseModules';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Modul Kursus';

    protected static ?string $modelLabel = 'Modul';

    protected static ?string $pluralModelLabel = 'Modul';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Nama Modul')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->maxLength(1000),

                TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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