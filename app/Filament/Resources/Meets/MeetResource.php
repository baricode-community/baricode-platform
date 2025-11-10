<?php

namespace App\Filament\Resources\Meets;

use App\Filament\Resources\Meets\Pages\CreateMeet;
use App\Filament\Resources\Meets\Pages\EditMeet;
use App\Filament\Resources\Meets\Pages\ListMeets;
use App\Filament\Resources\Meets\Schemas\MeetForm;
use App\Filament\Resources\Meets\Tables\MeetsTable;
use App\Models\Communication\Meet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class MeetResource extends Resource
{
    protected static ?string $model = Meet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur Dasar';

    protected static ?string $navigationLabel = 'Meet';

    public static function form(Schema $schema): Schema
    {
        return MeetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeetsTable::configure($table);
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
            'index' => ListMeets::route('/'),
            'create' => CreateMeet::route('/create'),
            'edit' => EditMeet::route('/{record}/edit'),
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
