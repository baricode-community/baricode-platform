<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'roles:admin'])
    ->prefix('admin')
    ->group(function () {
        Volt::route('', 'admin.index')->name('admin.index');
        Volt::route('meets', 'admin.courses.meet-management')->name('admin.meets');
        Volt::route('whatsapp-groups', 'admin.whatsapp-groups.index')->name('admin.whatsapp-groups');
        Volt::route('daily-quotes', 'admin.whatsapp-groups.daily-quotes')->name('admin.whatsapp-groups.daily-quotes');
    });
