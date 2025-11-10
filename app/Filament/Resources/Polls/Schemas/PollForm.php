<?php

namespace App\Filament\Resources\Polls\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class PollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')->required(),
                TextInput::make('description')->label('Deskripsi')->required(),
                Select::make('status')->label('Status')->options([
                    'closed' => 'Ditutup',
                    'open' => 'Dibuka',
                ])->required(),
                Select::make('user_id')
                    ->label('Dibuat Oleh')
                    ->options(\App\Models\Auth\User::pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
            ]);
    }
}
