<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'roles:admin'])
    ->prefix('admin-users')
    ->group(function () {
        Volt::route('', 'admin.user-management')->name('admin.users');
    });
