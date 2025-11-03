<?php

namespace App\Filament\Resources\Course\Courses;

use App\Filament\Resources\Course\Courses\Pages\ListCourses;
use App\Filament\Resources\Course\Courses\Pages\CreateCourse;
use App\Filament\Resources\Course\Courses\Pages\EditCourse;
use App\Filament\Resources\Course\Courses\RelationManagers\CourseModulesRelationManager;
use App\Filament\Resources\Course\Courses\Schemas\CourseForm;
use App\Filament\Resources\Course\Courses\Tables\CoursesTable;
use App\Models\Course\Course;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?string $navigationLabel = 'Kursus';

    protected static ?string $modelLabel = 'Kursus';

    protected static ?string $pluralModelLabel = 'Kursus';

    protected static ?int $navigationSort = 2;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Kursus';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema(CourseForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::getTable($table);
    }

    public static function getRelations(): array
    {
        return [
            CourseModulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}