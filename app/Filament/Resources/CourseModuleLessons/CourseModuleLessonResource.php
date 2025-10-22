<?php

namespace App\Filament\Resources\CourseModuleLessons;

use App\Filament\Resources\CourseModuleLessons\Pages\CreateCourseModuleLesson;
use App\Filament\Resources\CourseModuleLessons\Pages\EditCourseModuleLesson;
use App\Filament\Resources\CourseModuleLessons\Pages\ListCourseModuleLessons;
use App\Filament\Resources\CourseModuleLessons\Schemas\CourseModuleLessonForm;
use App\Filament\Resources\CourseModuleLessons\Tables\CourseModuleLessonsTable;
use App\Models\Course\CourseModuleLesson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseModuleLessonResource extends Resource
{
    protected static ?string $model = CourseModuleLesson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    
    protected static ?string $navigationLabel = 'Pelajaran';
    
    protected static ?string $modelLabel = 'Pelajaran';
    
    protected static ?string $pluralModelLabel = 'Pelajaran';
    
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CourseModuleLessonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseModuleLessonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseModuleLessons::route('/'),
            'create' => CreateCourseModuleLesson::route('/create'),
            'edit' => EditCourseModuleLesson::route('/{record}/edit'),
        ];
    }
}
