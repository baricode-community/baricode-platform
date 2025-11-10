<?php

namespace App\Filament\Resources\Course\CourseModules\RelationManagers;

use App\Models\Learning\CourseModuleLesson;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class CourseModuleLessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'courseModuleLessons';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Pelajaran';

    protected static ?string $modelLabel = 'Pelajaran';

    protected static ?string $pluralModelLabel = 'Pelajaran';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->label('Judul Pelajaran')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('content')
                    ->label('Konten')
                    ->maxLength(65535)
                    ->columnSpanFull(),

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
            ->recordTitleAttribute('title')
            ->columns([
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