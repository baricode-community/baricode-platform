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
        $sessions = CourseRecordSession::whereHas('courseEnrollment', function ($query) {
            $query->where('is_completed', false);
        })->get();

        logger()->info('Total CourseRecordSession found: ' . $sessions->count());

        foreach ($sessions as $session) {
            logger()->info("Processing session ID: {$session->id}");

            // Lalu mengecek apakah sekarang saatnya untuk belajar
            if (!$session->checkAndCreateAttendance()) {
                logger()->info("Not time to study for session ID: {$session->id}");
                continue;
            }

            $user = $session->courseEnrollment->user;
            logger()->info("Reminder sent to user: {$user->whatsapp}");

            // Membuat absensi untuk user pada sesi ini
            $session->attendances()->create([
            'user_id' => $user->id,
            'status' => 'absent',
            'recorded_at' => now(),
            ]);
        }
    }
}
