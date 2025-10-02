<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified'])
    ->prefix('flashcard')
    ->group(function () {
        Volt::route('', 'flashcard.index')->name('flashcard.index');
});