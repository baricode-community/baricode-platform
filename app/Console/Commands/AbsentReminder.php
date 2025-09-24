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
    protected $description = 'Send attendance reminders and create attendance records for active sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking active sessions for attendance reminders...');
        
        try {
            CourseRecordSession::checkAllIncompleteSessions();
            $this->info('Successfully processed all active sessions.');
        } catch (\Exception $e) {
            $this->error('Error occurred while processing sessions: ' . $e->getMessage());
            logger()->error('AbsentReminder command failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
