<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('blog')
    ->group(function () {
        Volt::route('', 'blog.index')->name('blog.index');
        Volt::route('{blog:slug}', 'blog.show')->name('blog.show');
});