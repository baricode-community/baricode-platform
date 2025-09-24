<?php

namespace App\Filament\Resources\LessonDetails;

use App\Filament\Resources\LessonDetails\Pages\CreateLessonDetail;
use App\Filament\Resources\LessonDetails\Pages\EditLessonDetail;
use App\Filament\Resources\LessonDetails\Pages\ListLessonDetails;
use App\Filament\Resources\LessonDetails\Schemas\LessonDetailForm;
use App\Filament\Resources\LessonDetails\Tables\LessonDetailsTable;
use App\Models\LessonDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LessonDetailResource extends Resource
{
    protected static ?string $model = LessonDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LessonDetailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonDetailsTable::configure($table);
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
            'index' => ListLessonDetails::route('/'),
            'create' => CreateLessonDetail::route('/create'),
            'edit' => EditLessonDetail::route('/{record}/edit'),
        ];
    }
}
