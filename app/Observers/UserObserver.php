<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        logger()->info('User created: ' . $user->email);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        logger()->info('User updated: ' . $user->email);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        logger()->info('User deleted: ' . $user->email);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        logger()->info('User restored: ' . $user->email);
    }

    /**
     * Handle the User "forceDeleted" event.
     */
    public function forceDeleted(User $user): void
    {
        logger()->info('User force deleted: ' . $user->email);
    }
}