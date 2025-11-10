<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Models\Meet;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupSoftDeletedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baricode:cleanup-soft-deleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        logger()->info('Starting cleanup of soft-deleted data...');
        $this->info('Starting cleanup of soft-deleted data...');

        // Users
        $usersDeleted = User::onlyTrashed()
            ->where('deleted_at', '<=', Carbon::now()->subDays(30))
            ->get();
        logger()->info("Found {$usersDeleted->count()} users to permanently delete because they were soft-deleted over 30 days ago.");
        $usersDeleted->each(function ($user) {
            $this->info("Permanently deleting user ID {$user->id}...");
            logger()->notice("Permanently deleting user ID {$user->id}...");
            $user->forceDelete();
        });

        $this->info('Cleanup completed.');
        logger()->info('Cleanup of soft-deleted data completed.');
    }
}
