<?php

namespace App\Console\Commands;

use App\Models\CourseRecordSession;
use Illuminate\Console\Command;

class AbsentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baricode:absent-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        logger()->info('AbsentReminder command executed');

        // Mengambil semua session yang is_approved nya false
        CourseRecordSession::whereHas('courseRecord', function ($query) {
            $query->where('is_approved', false);
        })->get()->each(function ($session) {
            logger()->info("Session ID: {$session->id}");

            // Lalu mengecek apakah sekarang saatnya untuk belajar
            if (!$session->isTimeToStudy()) {
                logger()->info("Not time to study for session ID: {$session->id}");
                return;
            }

            $user = $session->courseRecord->user;
            logger()->info("Reminder sent to user: {$user->whatsapp}");
        });
    }
}
