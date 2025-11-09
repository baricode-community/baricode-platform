<?php

namespace App\Jobs;

use App\Models\Habit;
use App\Models\HabitSchedule;
use App\Models\User\User;
use App\Notifications\HabitReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendHabitReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Habit $habit,
        public HabitSchedule $schedule,
        public User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if habit is still active
            if (!$this->habit->isActive()) {
                Log::info("Habit {$this->habit->id} is not active, skipping reminder");
                return;
            }

            // Check if user is still a participant
            if (!$this->habit->hasParticipant($this->user->id)) {
                Log::info("User {$this->user->id} is no longer a participant of habit {$this->habit->id}");
                return;
            }

            // Check if user already logged for today
            $todayLog = $this->habit->logs()
                ->where('user_id', $this->user->id)
                ->where('log_date', today())
                ->first();

            if ($todayLog) {
                Log::info("User {$this->user->id} already logged for habit {$this->habit->id} today");
                return;
            }

            // Send notification
            $this->user->notify(new HabitReminderNotification($this->habit, $this->schedule));

            Log::info("Habit reminder sent to user {$this->user->id} for habit {$this->habit->id}");

        } catch (\Exception $e) {
            Log::error("Failed to send habit reminder: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendHabitReminder job failed: " . $exception->getMessage());
    }
}
