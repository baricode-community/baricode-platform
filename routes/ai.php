<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified'])
    ->prefix('ai')
    ->group(function () {
        Volt::route('', 'ai.index')->name('ai.index');
});