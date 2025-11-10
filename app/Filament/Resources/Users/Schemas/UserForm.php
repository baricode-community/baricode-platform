<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Pengguna')->required()->maxLength(255),
                TextInput::make('email')->label('Surel')->email()->maxLength(255),
                Textarea::make('about')->label('Tentang')->rows(3)->maxLength(65535),
                TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(20),
                TextInput::make('password')->label('Kata Sandi')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => filled($state))
                    ->visibleOn('create'),
                \Filament\Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
            ]);
    }
}
