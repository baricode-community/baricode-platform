<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseRecordSession;

class CheckSessionAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all incomplete course record sessions and create attendance records if it is time for the session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking incomplete sessions for attendance creation...');
        
        try {
            CourseRecordSession::checkAllIncompleteSessions();
            $this->info('Successfully checked all incomplete sessions.');
        } catch (\Exception $e) {
            $this->error('Error occurred while checking sessions: ' . $e->getMessage());
            logger()->error('CheckSessionAttendance command failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
