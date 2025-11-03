<?php

namespace App\Filament\Resources\Course\CourseCategories;

use App\Filament\Resources\Course\CourseCategories\Pages\ListCourseCategories;
use App\Filament\Resources\Course\CourseCategories\Pages\CreateCourseCategory;
use App\Filament\Resources\Course\CourseCategories\Pages\EditCourseCategory;
use App\Filament\Resources\Course\CourseCategories\RelationManagers\CoursesRelationManager;
use App\Filament\Resources\Course\CourseCategories\Schemas\CourseCategoryForm;
use App\Filament\Resources\Course\CourseCategories\Tables\CourseCategoriesTable;
use App\Models\Course\CourseCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CourseCategoryResource extends Resource
{
    protected static ?string $model = CourseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Kategori Kursus';

    protected static ?string $modelLabel = 'Kategori Kursus';

    protected static ?string $pluralModelLabel = 'Kategori Kursus';

    protected static ?int $navigationSort = 1;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Kursus';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema(CourseCategoryForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return CourseCategoriesTable::getTable($table);
    }

    public static function getRelations(): array
    {
        return [
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseCategories::route('/'),
            'create' => CreateCourseCategory::route('/create'),
            'edit' => EditCourseCategory::route('/{record}/edit'),
        ];
    }
}