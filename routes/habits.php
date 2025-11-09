<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified'])
    ->prefix('satu-tapak')
    ->group(function () {
        // Main pages
        Volt::route('/', 'habits.index')->name('satu-tapak.index');
        Volt::route('create', 'habits.create')->name('satu-tapak.create');
        
        // Invitations
        Volt::route('invitations', 'habits.invitations')->name('satu-tapak.invitations.index');
        Volt::route('/{habitId}', 'habits.show')->name('satu-tapak.show');
        
        // Habit specific actions
        Volt::route('/{habitId}/invite', 'habits.invite')->name('satu-tapak.invite');
        Volt::route('/{habitId}/statistics', 'habits.statistics')->name('satu-tapak.statistics');
        Volt::route('/{habitId}/edit', 'habits.edit')->name('satu-tapak.edit');
    });
