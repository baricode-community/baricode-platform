<?php

namespace App\Filament\Resources\Polls;

use App\Filament\Resources\Polls\Pages\CreatePoll;
use App\Filament\Resources\Polls\Pages\EditPoll;
use App\Filament\Resources\Polls\Pages\ListPolls;
use App\Filament\Resources\Polls\Schemas\PollForm;
use App\Filament\Resources\Polls\Tables\PollsTable;
use App\Models\Content\Poll;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PollResource extends Resource
{
    protected static ?string $model = Poll::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Fitur Dasar';

    protected static ?string $navigationLabel = 'Poll Pendapat';

    public static function form(Schema $schema): Schema
    {
        return PollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PollsTable::configure($table);
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
            'index' => ListPolls::route('/'),
            'create' => CreatePoll::route('/create'),
            'edit' => EditPoll::route('/{record}/edit'),
        ];
    }
}
