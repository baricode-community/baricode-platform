<?php

namespace App\Providers;

use App\Models\User\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('admin');
        });
        // LogViewer::auth(function ($request) {
        //     $user = $request->user();

        //     return $user->hasRole('admin');
        // });
    }
}
