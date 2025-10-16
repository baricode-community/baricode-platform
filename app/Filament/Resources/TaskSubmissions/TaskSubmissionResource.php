<?php

namespace App\Filament\Resources\TaskSubmissions;

use App\Filament\Resources\TaskSubmissions\Pages\CreateTaskSubmission;
use App\Filament\Resources\TaskSubmissions\Pages\EditTaskSubmission;
use App\Filament\Resources\TaskSubmissions\Pages\ListTaskSubmissions;
use App\Filament\Resources\TaskSubmissions\Schemas\TaskSubmissionForm;
use App\Filament\Resources\TaskSubmissions\Tables\TaskSubmissionsTable;
use App\Models\TaskSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TaskSubmissionResource extends Resource
{
    protected static ?string $model = TaskSubmission::class;

    protected static ?string $navigationLabel = 'Review Submissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function form(Schema $schema): Schema
    {
        return TaskSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskSubmissionsTable::configure($table);
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
            'index' => ListTaskSubmissions::route('/'),
            'create' => CreateTaskSubmission::route('/create'),
            'edit' => EditTaskSubmission::route('/{record}/edit'),
        ];
    }
}
