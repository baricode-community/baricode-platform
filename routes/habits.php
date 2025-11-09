<?php

use App\Http\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('satu-tapak')
    ->controller(HabitController::class)
    ->group(function () {
        // Dasar
        Route::get('/', 'index')->name('satu-tapak.index');
        Route::get('create', 'create')->name('satu-tapak.create');
        Route::get('{habit}', 'show')->name('satu-tapak.show');
        Route::post('/', 'store')->name('satu-tapak.store');

        // Undangan
        Route::get('invitations', 'invitations')->name('satu-tapak.invitations.index');
        Route::post('invitations/{invitation}/respond', 'respondInvitation')->name('satu-tapak.invitations.respond');

        // Additional Habit Routes
        Route::post('habits/{habit}/lock','lock')->name('satu-tapak.lock');
        Route::get('habits/{habit}/invite','invite')->name('satu-tapak.invite');
        Route::post('habits/{habit}/invite','sendInvitation')->name('satu-tapak.send-invitation');
        Route::post('habits/{habit}/log','log')->name('satu-tapak.log');
        Route::get('habits/{habit}/statistics','statistics')->name('satu-tapak.statistics');

        // Uncomment if needed
        // Route::get('{habit}/edit', 'edit')->name('satu-tapak.edit');
        // Route::put('habits/{habit}', 'update')->name('satu-tapak.update');
        // Route::delete('habits/{habit}', 'destroy')->name('satu-tapak.destroy');
    });
