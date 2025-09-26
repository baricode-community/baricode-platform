<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::controller(\App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::redirect('/tos', '/terms-of-service');
    Route::get('/terms-of-service', 'tos')->name('tos');
    Route::get('/about', 'about')->name('about');
    Route::get('/cara-belajar', 'cara_belajar')->name('cara_belajar');
    
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

Route::controller(\App\Http\Controllers\UserController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('users')
    ->group(function () {
        Route::get('/', 'index')->name('users');
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

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // Admin routes for course management
    Route::prefix('admin')->name('admin.')->middleware('roles:admin')->group(function () {
        Volt::route('users', 'admin.user-management')->name('users');
        Volt::route('courses', 'admin.course-management')->name('courses');
        Volt::route('categories', 'admin.category-management')->name('categories');
        Volt::route('modules', 'admin.module-management')->name('modules');
        Volt::route('lessons', 'admin.lesson-management')->name('lessons');
        
        // Individual course module management
        Route::get('courses/{course}/modules', function ($courseId) {
            return view('livewire.admin.course-modules', ['courseId' => $courseId]);
        })->name('course.modules');
        
        // Individual module lesson management
        Route::get('modules/{module}/lessons', function ($moduleId) {
            return view('livewire.admin.module-lessons', ['moduleId' => $moduleId]);
        })->name('module.lessons');
    });
});

require __DIR__.'/auth.php';
