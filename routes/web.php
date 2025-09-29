<?php

use App\Http\Controllers\AdminController;
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

// Meet routes - Livewire Volt routes
Route::middleware(['web'])->group(function () {
    Volt::route('meets', 'meets.index')->name('meets.index');
    Volt::route('meets/{meet}', 'meets.show')->name('meets.show');
});

Route::controller(AdminController::class)
    ->middleware(['auth', 'roles:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/', 'index')->name('admin');
        
        // New structured course management routes
        Route::prefix('course-management')->group(function () {
            // Course Categories
            Route::resource('course-categories', \App\Http\Controllers\Admin\CourseCategoryController::class, ['as' => 'admin']);
            Route::get('course-categories/{courseCategory}/courses', [\App\Http\Controllers\Admin\CourseCategoryController::class, 'courses'])->name('admin.course-categories.courses');

            // Courses
            Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class, ['as' => 'admin']);
            Route::get('courses/{course}/modules', [\App\Http\Controllers\Admin\CourseController::class, 'modules'])->name('admin.courses.modules');
            Route::post('courses/{course}/modules/reorder', [\App\Http\Controllers\Admin\CourseModuleController::class, 'reorder'])->name('admin.course-modules.reorder');

            // Modules
            Route::resource('course-modules', \App\Http\Controllers\Admin\CourseModuleController::class, ['as' => 'admin']);
            Route::get('course-modules/{courseModule}/lessons', [\App\Http\Controllers\Admin\CourseModuleController::class, 'lessons'])->name('admin.course-modules.lessons');

            // Lessons
            Route::resource('course-module-lessons', \App\Http\Controllers\Admin\CourseModuleLessonController::class, ['as' => 'admin']);
            Route::post('course-modules/{courseModule}/lessons/reorder', [\App\Http\Controllers\Admin\CourseModuleLessonController::class, 'reorder'])->name('admin.course-module-lessons.reorder');
        });
        
        // Legacy Volt routes (keep for backward compatibility)
        Volt::route('users', 'admin.courses.user-management')->name('admin.users');
        // Volt::route('courses', 'admin.courses.course-management')->name('admin.courses.old');
        // Volt::route('categories', 'admin.courses.category-management')->name('admin.courses.categories.old');
        // Volt::route('modules', 'admin.courses.module-management')->name('admin.courses.modules.old');
        // Volt::route('lessons', 'admin.courses.lesson-management')->name('admin.courses.lessons.old');
        Volt::route('meets', 'admin.courses.meet-management')->name('admin.meets');

        // Individual course module management (legacy)
        Route::get('courses/{course}/modules', function ($courseId) {
            return view('livewire.admin.courses.course-modules', ['courseId' => $courseId]);
        })->name('course.modules.old');

        // Individual module lesson management (legacy)
        Route::get('modules/{module}/lessons', function ($moduleId) {
            return view('livewire.admin.courses.module-lessons', ['moduleId' => $moduleId]);
        })->name('module.lessons.old');
    });

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
