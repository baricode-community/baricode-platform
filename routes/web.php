<?php

use Illuminate\Support\Facades\Route;
use Laravel\Folio\Folio;
use Livewire\Volt\Volt;

Folio::path(resource_path('views/folio'))->uri('/');

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::controller(\App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/profile', 'profile')->name('profile');
    });

Route::controller(\App\Http\Controllers\CourseController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('dashboard/courses')
    ->group(function () {
        Route::get('/start/{course}', 'start')->name('course.start');
        Route::get('/continue/{course:slug}', 'continue')->name('course.continue');
    });

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
