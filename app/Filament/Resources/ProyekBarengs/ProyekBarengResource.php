<?php

namespace App\Filament\Resources\ProyekBarengs;

use App\Filament\Resources\ProyekBarengs\Pages\CreateProyekBareng;
use App\Filament\Resources\ProyekBarengs\Pages\EditProyekBareng;
use App\Filament\Resources\ProyekBarengs\Pages\ListProyekBarengs;
use App\Filament\Resources\ProyekBarengs\Schemas\ProyekBarengForm;
use App\Filament\Resources\ProyekBarengs\Tables\ProyekBarengsTable;
use App\Models\ProyekBareng;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProyekBarengResource extends Resource
{
    protected static ?string $model = ProyekBareng::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;
    protected static string|UnitEnum|null $navigationGroup = 'Fitur Dasar';


    public static function form(Schema $schema): Schema
    {
        return ProyekBarengForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProyekBarengsTable::configure($table);
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
            'index' => ListProyekBarengs::route('/'),
            'create' => CreateProyekBareng::route('/create'),
            'edit' => EditProyekBareng::route('/{record}/edit'),
        ];
    }
}
