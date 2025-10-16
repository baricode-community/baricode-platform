<?php

namespace App\Filament\Resources\Meets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MeetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Title')->required()->maxLength(255),
                TextInput::make('youtube_link')->label('Youtube Link')->maxLength(255),
                TextInput::make('meet_link')->label('Meet Link')->maxLength(255),
                Toggle::make('is_finished')
                    ->label('Apakah Pertemuan Telah Selesai?')
                    ->inline(false)
                    ->default(false),
                TextInput::make('description')->label('Description')->maxLength(65535),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At')
                    ->seconds(false),
                Select::make('user_id')
                    ->label('Peserta')
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
