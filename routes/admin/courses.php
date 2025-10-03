<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'roles:admin'])
    ->prefix('admin-courses')
    ->group(function () {
        Volt::route('', 'admin.courses.index')->name('admin.courses.index');
        Volt::route('category/{courseCategory}', 'admin.courses.category')->name('admin.courses.category');

        // // New structured course management routes
        // Route::prefix('course-management')->group(function () {
        //     // Course Categories
        //     Route::resource('course-categories', \App\Http\Controllers\Admin\CourseCategoryController::class, ['as' => 'admin']);
        //     Route::get('course-categories/{courseCategory}/courses', [\App\Http\Controllers\Admin\CourseCategoryController::class, 'courses'])->name('admin.course-categories.courses');

        //     // Courses
        //     Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class, ['as' => 'admin']);
        //     Route::get('courses/{course}/modules', [\App\Http\Controllers\Admin\CourseController::class, 'modules'])->name('admin.courses.modules');
        //     Route::post('courses/{course}/modules/reorder', [\App\Http\Controllers\Admin\CourseModuleController::class, 'reorder'])->name('admin.course-modules.reorder');

        //     // Modules
        //     Route::resource('course-modules', \App\Http\Controllers\Admin\CourseModuleController::class, ['as' => 'admin']);
        //     Route::get('course-modules/{courseModule}/lessons', [\App\Http\Controllers\Admin\CourseModuleController::class, 'lessons'])->name('admin.course-modules.lessons');

        //     // Lessons
        //     Route::resource('course-module-lessons', \App\Http\Controllers\Admin\CourseModuleLessonController::class, ['as' => 'admin']);
        //     Route::post('course-modules/{courseModule}/lessons/reorder', [\App\Http\Controllers\Admin\CourseModuleLessonController::class, 'reorder'])->name('admin.course-module-lessons.reorder');
        // });

        // // Individual course module management (legacy)
        // Route::get('courses/{course}/modules', function ($courseId) {
        //     return view('livewire.admin.courses.course-modules', ['courseId' => $courseId]);
        // })->name('course.modules.old');

        // // Individual module lesson management (legacy)
        // Route::get('modules/{module}/lessons', function ($moduleId) {
        //     return view('livewire.admin.courses.module-lessons', ['moduleId' => $moduleId]);
        // })->name('module.lessons.old');
    });
