<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('email')->label('Email')->email()->required(),
                TextInput::make('whatsapp')->label('Nomor WhatsApp')->tel(),
                Select::make('level')->label('Level')->options([
                    'pemula' => 'Pemula',
                    'menengah' => 'Menengah',
                    'mahir' => 'Mahir',
                ])->required(),
                Textarea::make('about')->label('Tentang Saya'),
                TextInput::make('email_verified_at')
                    ->label('Email Terverifikasi Pada')
                    ->disabled(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
            ]);
    }
}
