<?php

namespace App\Filament\Resources\Course\CourseModules;

use App\Filament\Resources\Course\CourseModules\Pages\ListCourseModules;
use App\Filament\Resources\Course\CourseModules\Pages\CreateCourseModule;
use App\Filament\Resources\Course\CourseModules\Pages\EditCourseModule;
use App\Filament\Resources\Course\CourseModules\RelationManagers\CourseModuleLessonsRelationManager;
use App\Filament\Resources\Course\CourseModules\Schemas\CourseModuleForm;
use App\Filament\Resources\Course\CourseModules\Tables\CourseModulesTable;
use App\Models\Learning\CourseModule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CourseModuleResource extends Resource
{
    protected static ?string $model = CourseModule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QueueList;

    protected static ?string $navigationLabel = 'Modul';

    protected static ?string $modelLabel = 'Modul';

    protected static ?string $pluralModelLabel = 'Modul';

    protected static ?int $navigationSort = 3;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Kursus';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema(CourseModuleForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return CourseModulesTable::getTable($table);
    }

    public static function getRelations(): array
    {
        return [
            CourseModuleLessonsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseModules::route('/'),
            'create' => CreateCourseModule::route('/create'),
            'edit' => EditCourseModule::route('/{record}/edit'),
        ];
    }
}