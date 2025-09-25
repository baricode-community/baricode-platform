<?php

namespace App\Filament\Resources\UserNotes;

use App\Filament\Resources\UserNotes\Pages\CreateUserNote;
use App\Filament\Resources\UserNotes\Pages\EditUserNote;
use App\Filament\Resources\UserNotes\Pages\ListUserNotes;
use App\Filament\Resources\UserNotes\Schemas\UserNoteForm;
use App\Filament\Resources\UserNotes\Tables\UserNotesTable;
use App\Models\User\UserNote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserNoteResource extends Resource
{
    protected static ?string $model = UserNote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserNoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserNotesTable::configure($table);
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
            'index' => ListUserNotes::route('/'),
            'create' => CreateUserNote::route('/create'),
            'edit' => EditUserNote::route('/{record}/edit'),
        ];
    }
}
