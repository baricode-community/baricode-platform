<?php

use App\Http\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('satu-tapak')
    ->name('satu-tapak.')
    ->group(function () {
        
        // Habit Routes
        Route::resource('habits', HabitController::class);
        
        // Additional Habit Routes
        Route::post('habits/{habit}/lock', [HabitController::class, 'lock'])
            ->name('habits.lock');
            
        Route::get('habits/{habit}/invite', [HabitController::class, 'invite'])
            ->name('habits.invite');
            
        Route::post('habits/{habit}/invite', [HabitController::class, 'sendInvitation'])
            ->name('habits.send-invitation');
            
        Route::get('invitations', [HabitController::class, 'invitations'])
            ->name('invitations.index');
            
        Route::post('invitations/{invitation}/respond', [HabitController::class, 'respondInvitation'])
            ->name('invitations.respond');
            
        Route::post('habits/{habit}/log', [HabitController::class, 'log'])
            ->name('habits.log');
            
        Route::get('habits/{habit}/statistics', [HabitController::class, 'statistics'])
            ->name('habits.statistics');
    });