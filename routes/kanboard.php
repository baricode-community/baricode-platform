<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('kanboard')->middleware(['auth'])->group(function () {
    // Main kanboard routes
    Volt::route('', 'kanboard.index')->name('kanboard.index');
    Volt::route('create', 'kanboard.create')->name('kanboard.create');
    Volt::route('{kanboard:board_id}', 'kanboard.show')->name('kanboard.show');
    Volt::route('{kanboard:board_id}/edit', 'kanboard.edit')->name('kanboard.edit');
    Volt::route('{kanboard:board_id}/settings', 'kanboard.settings')->name('kanboard.settings');
    
    // User management routes
    Volt::route('{kanboard:board_id}/users', 'kanboard.users')->name('kanboard.users');
    Volt::route('{kanboard:board_id}/users/invite', 'kanboard.users-invite')->name('kanboard.users.invite');
    
    // Card and todo management (handled via Livewire components in show view)
    // Volt::route('{kanboard:slug}/cards/{card}', 'kanboard.card-detail')->name('kanboard.card.detail');
});