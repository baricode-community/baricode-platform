<?php

namespace App\Console\Commands;

use App\Jobs\SendHabitReminder;
use App\Models\Habits\Habit;
use App\Models\Habits\HabitSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendHabitReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'habits:send-reminders {--test : Test mode - show what would be sent without actually sending}';

    /**
     * The console command description.
     */
    protected $description = 'Send habit reminders based on scheduled times';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isTest = $this->option('test');
        $currentDay = strtolower(Carbon::now()->format('l')); // Get current day name (e.g., 'monday')
        $currentTime = Carbon::now()->format('H:i');
        
        $this->info("Checking for habit reminders...");
        $this->info("Current day: {$currentDay}");
        $this->info("Current time: {$currentTime}");
        
        if ($isTest) {
            $this->warn("TEST MODE - No reminders will be actually sent");
        }

        // Find schedules that should trigger reminders now
        $schedules = HabitSchedule::where('day_of_week', $currentDay)
            ->where('is_active', true)
            ->with(['habit.approvedParticipants.user'])
            ->get();

        $remindersSent = 0;
        $remindersSkipped = 0;

        foreach ($schedules as $schedule) {
            $habit = $schedule->habit;
            
            // Skip if habit is not active
            if (!$habit->isActive()) {
                $this->line("Skipping inactive habit: {$habit->name} (ID: {$habit->id})");
                continue;
            }

            // Check if this is the right time (within 5 minutes of scheduled time)
            $scheduledTime = Carbon::createFromTimeString($schedule->scheduled_time);
            $timeDiff = Carbon::now()->diffInMinutes($scheduledTime, false);
            
            // Only send if we're within 5 minutes of the scheduled time
            if (abs($timeDiff) > 5) {
                continue;
            }

            $this->line("Processing habit: {$habit->name} (ID: {$habit->id})");
            $this->line("Scheduled time: {$schedule->formatted_time}");

            // Send reminders to all participants
            foreach ($habit->approvedParticipants as $participant) {
                $user = $participant->user;
                
                // Check if user already logged today
                $todayLog = $habit->logs()
                    ->where('user_id', $user->id)
                    ->where('log_date', today())
                    ->first();

                if ($todayLog) {
                    $this->line("  - Skipping {$user->name} (already logged today)");
                    $remindersSkipped++;
                    continue;
                }

                if ($isTest) {
                    $this->info("  - Would send reminder to: {$user->name} ({$user->email})");
                } else {
                    try {
                        SendHabitReminder::dispatch($habit, $schedule, $user);
                        $this->info("  - Queued reminder for: {$user->name}");
                        $remindersSent++;
                    } catch (\Exception $e) {
                        $this->error("  - Failed to queue reminder for {$user->name}: " . $e->getMessage());
                        Log::error("Failed to dispatch habit reminder", [
                            'habit_id' => $habit->id,
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        if ($isTest) {
            $this->info("\nTest completed. Found {$schedules->count()} schedules for today.");
        } else {
            $this->info("\nReminder processing completed!");
            $this->info("Reminders sent: {$remindersSent}");
            $this->info("Reminders skipped: {$remindersSkipped}");
        }

        Log::info("Habit reminders processed", [
            'sent' => $remindersSent,
            'skipped' => $remindersSkipped,
            'test_mode' => $isTest
        ]);
    }
}
