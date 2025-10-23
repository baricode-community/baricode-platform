<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::controller(\App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/platform', 'super_app')->name('home.super-app');
    Route::redirect('/tos', '/terms-of-service');
    Route::get('/terms-of-service', 'tos')->name('tos');
    Route::get('/about', 'about')->name('about');
    Route::get('/cara-belajar', 'cara_belajar')->name('cara_belajar');

    Route::get('/profile/{user}', 'profile')->name('profile_user');

    Route::prefix('courses')->group(function () {
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

Route::controller(\App\Http\Controllers\TaskController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('tasks')
    ->group(function () {
        Route::get('/', 'index')->name('tasks.index');
        Route::get('/submission/{id}', 'viewSubmission')->name('tasks.submission.view');
        Route::get('/submissions', 'submissions')->name('tasks.submissions');
        Route::get('/{id}/{assignmentId?}', 'show')->name('tasks.show');
        Route::post('/{id}/submit', 'submit')->name('tasks.submit');
    });

Route::controller(\App\Http\Controllers\UserController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('users')
    ->group(function () {
        Route::get('/', 'index')->name('users');
    });

Route::controller(\App\Http\Controllers\TimeTrackerController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('time-tracker')
    ->group(function () {
        Route::get('/', 'index')->name('time-tracker.index');
    });

Route::controller(\App\Http\Controllers\CourseController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('courses')
    ->group(function () {
        Route::get('/prepare/{course:slug}', 'prepare')->name('course.prepare');
        Route::post('/start/{course:slug}', 'start')->name('course.start');
        Route::get('/continue/{enrollment}', 'continue')->name('course.continue');
        Route::get('/continue/lesson/{enrollmentLesson}', 'continue_lesson')->name('course.continue.lesson');
    });

// Meet routes - Livewire Volt routes
Route::middleware(['web'])->group(function () {
    Volt::route('meets', 'meets.index')->name('meets.index');
    Volt::route('meets/{meet}', 'meets.show')->name('meets.show');
});


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';

// Admin routes
// require __DIR__.'/admin/basic.php';
// require __DIR__.'/admin/courses.php';
// require __DIR__.'/admin/users.php';
// require __DIR__.'/admin/blog.php';

require __DIR__.'/blog.php';
require __DIR__.'/ai.php';
require __DIR__.'/tube.php';
require __DIR__.'/flashcard.php';