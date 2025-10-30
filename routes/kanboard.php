<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('kanboard')
    ->group(function () {
        Volt::route('', 'kanboard.index')->name('kanboard.index');
        Volt::route('{kanban:slug}', 'kanboard.show')->name('kanboard.show');
});