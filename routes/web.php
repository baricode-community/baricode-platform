<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::controller(\App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/cara-belajar', 'cara_belajar')->name('cara_belajar');
    
    Route::prefix('course')->group(function () {
        Route::get('/', 'courses')->name('courses');
        Route::get('/{course:slug}', 'course')->name('course.show');

        // Kategorisasi course berdasarkan level
        Route::prefix('level')->group(function () {
            Route::get('/pemula', 'pemula')->name('courses.pemula');
            Route::get('/menengah', 'menengah')->name('courses.menengah');
            Route::get('/lanjut', 'lanjut')->name('courses.lanjut');
        });
    });
});

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
        Route::get('/prepare/{course:slug}', 'prepare')->name('course.prepare');
        Route::post('/start/{course:slug}', 'start')->name('course.start');
        Route::get('/continue/{courseRecord}', 'continue')->name('course.continue');
        Route::get('/continue/{courseRecord}/{lesson}', 'continue_lesson')->name('course.continue.lesson');
        Route::post('/continue/{courseRecord}/{lesson}', 'continue_lesson_markAsLearned')->name('course.continue.lesson.markAsLearned');
    });

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
