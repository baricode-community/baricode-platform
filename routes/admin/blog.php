<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'roles:admin'])
    ->prefix('admin/blog')
    ->name('admin.blog.')
    ->group(function () {
        Volt::route('', 'admin.blog-management.index')->name('index');
        Volt::route('create', 'admin.blog-management.create')->name('create');
        Volt::route('{blog}/edit', 'admin.blog-management.edit')->name('edit');
    });
