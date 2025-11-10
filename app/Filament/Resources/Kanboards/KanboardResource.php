<?php

namespace App\Filament\Resources\Kanboards;

use App\Filament\Resources\Kanboards\Pages\CreateKanboard;
use App\Filament\Resources\Kanboards\Pages\EditKanboard;
use App\Filament\Resources\Kanboards\Pages\ListKanboards;
use App\Filament\Resources\Kanboards\Schemas\KanboardForm;
use App\Filament\Resources\Kanboards\Tables\KanboardsTable;
use App\Models\Projects\Kanboard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KanboardResource extends Resource
{
    protected static ?string $model = Kanboard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BoltSlash;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur Dasar';

    protected static ?string $navigationLabel = 'Kanban Board';

    public static function form(Schema $schema): Schema
    {
        return KanboardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KanboardsTable::configure($table);
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
            'index' => ListKanboards::route('/'),
            'create' => CreateKanboard::route('/create'),
            'edit' => EditKanboard::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
