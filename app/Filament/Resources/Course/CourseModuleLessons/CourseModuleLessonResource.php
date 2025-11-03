<?php

namespace App\Filament\Resources\Course\CourseModuleLessons;

use App\Filament\Resources\Course\CourseModuleLessons\Pages\ListCourseModuleLessons;
use App\Filament\Resources\Course\CourseModuleLessons\Pages\CreateCourseModuleLesson;
use App\Filament\Resources\Course\CourseModuleLessons\Pages\EditCourseModuleLesson;
use App\Filament\Resources\Course\CourseModuleLessons\Schemas\CourseModuleLessonForm;
use App\Filament\Resources\Course\CourseModuleLessons\Tables\CourseModuleLessonsTable;
use App\Models\Course\CourseModuleLesson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CourseModuleLessonResource extends Resource
{
    protected static ?string $model = CourseModuleLesson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = 'Pelajaran';

    protected static ?string $modelLabel = 'Pelajaran';

    protected static ?string $pluralModelLabel = 'Pelajaran';

    protected static ?int $navigationSort = 4;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Kursus';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema(CourseModuleLessonForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return CourseModuleLessonsTable::getTable($table);
    }

    public static function getRelations(): array
    {
        return [
            // CourseModuleLesson tidak memiliki child relations
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