<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified'])
    ->prefix('tube')
    ->group(function () {
        Volt::route('', 'tube.index')->name('tube.index');
});