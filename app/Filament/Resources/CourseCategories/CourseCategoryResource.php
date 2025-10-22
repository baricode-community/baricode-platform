<?php

namespace App\Filament\Resources\CourseCategories;

use App\Filament\Resources\CourseCategories\Pages\CreateCourseCategory;
use App\Filament\Resources\CourseCategories\Pages\EditCourseCategory;
use App\Filament\Resources\CourseCategories\Pages\ListCourseCategories;
use App\Filament\Resources\CourseCategories\Schemas\CourseCategoryForm;
use App\Filament\Resources\CourseCategories\Tables\CourseCategoriesTable;
use App\Models\Course\CourseCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseCategoryResource extends Resource
{
    protected static ?string $model = CourseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    
    protected static ?string $navigationLabel = 'Kategori Kursus';
    
    protected static ?string $modelLabel = 'Kategori Kursus';
    
    protected static ?string $pluralModelLabel = 'Kategori Kursus';
    
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CourseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CoursesRelationManager::class,
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
