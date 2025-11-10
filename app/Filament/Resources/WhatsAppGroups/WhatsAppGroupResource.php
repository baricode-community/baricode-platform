<?php

namespace App\Filament\Resources\WhatsAppGroups;

use App\Filament\Resources\WhatsAppGroups\Pages\CreateWhatsAppGroup;
use App\Filament\Resources\WhatsAppGroups\Pages\EditWhatsAppGroup;
use App\Filament\Resources\WhatsAppGroups\Pages\ListWhatsAppGroups;
use App\Filament\Resources\WhatsAppGroups\Schemas\WhatsAppGroupForm;
use App\Filament\Resources\WhatsAppGroups\Tables\WhatsAppGroupsTable;
use App\Models\Communication\WhatsAppGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WhatsAppGroupResource extends Resource
{
    protected static ?string $model = WhatsAppGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Grup WhatsApp';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return WhatsAppGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhatsAppGroupsTable::configure($table);
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
            'index' => ListWhatsAppGroups::route('/'),
            'create' => CreateWhatsAppGroup::route('/create'),
            'edit' => EditWhatsAppGroup::route('/{record}/edit'),
        ];
    }
}
