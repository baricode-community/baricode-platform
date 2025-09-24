<?php

namespace App\Traits;

use App\Models\CourseRecordSession;
use App\Models\CourseAttendance;
use Carbon\Carbon;

trait CourseRecordSessionTrait
{
    public function getNamaHari(): string
    {
        $days = [
            1 => 'Ahad',
            2 => 'Senin',
            3 => 'Selasa',
            4 => 'Rabu',
            5 => 'Kamis',
            6 => 'Jumat',
            7 => 'Sabtu',
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Check if it's time for session and create attendance records if needed
     */
    public function checkAndCreateAttendance(): bool
    {
        $currentTime = now();
        $currentDayOfWeek = $currentTime->dayOfWeek == 0 ? 1 : $currentTime->dayOfWeek + 1; // 0 (Sun) => 1 (Ahad), 1-6 => 2-7
        
        // Check if today matches the session day
        if ($this->day_of_week !== $currentDayOfWeek) {
            return false;
        }
        
        $reminders = [$this->reminder_1, $this->reminder_2, $this->reminder_3];
        
        // Check if current time is within 5 minutes of any reminder time
        foreach ($reminders as $reminder) {
            if (empty($reminder)) continue;
            
            try {
                // Handle both H:i and H:i:s format
                $reminderTimeString = is_string($reminder) ? $reminder : $reminder->format('H:i');
                $reminderTime = Carbon::createFromFormat('H:i', substr($reminderTimeString, 0, 5));
                $timeDifference = abs($currentTime->diffInMinutes($reminderTime));
                
                // If within 5 minutes of reminder time
                if ($timeDifference <= 5) {
                    $this->createAttendanceForAllStudents();
                    return true;
                }
            } catch (\Exception $e) {
                logger()->warning("Failed to parse reminder time: {$reminder}. Error: " . $e->getMessage());
                continue;
            }
        }
        
        return false;
    }

    /**
     * Create attendance records for all students enrolled in the course
     */
    private function createAttendanceForAllStudents(): void
    {
        // Get all students enrolled in the course through course enrollments
        $courseEnrollments = $this->courseEnrollment->course->courseEnrollments()
            ->where('is_approved', true)
            ->with('user')
            ->get();
        
        foreach ($courseEnrollments as $enrollment) {
            // Check if attendance already exists for this session and student
            $existingAttendance = CourseAttendance::where('course_record_session_id', $this->id)
                ->where('student_id', $enrollment->user->id)
                ->first();
            
            if (!$existingAttendance) {
                CourseAttendance::create([
                    'course_id' => $this->courseEnrollment->course->id,
                    'course_record_session_id' => $this->id,
                    'student_id' => $enrollment->user->id,
                    'status' => CourseAttendance::STATUS_BELUM,
                    'absent_date' => now()->format('Y-m-d'),
                    'notes' => 'Auto-generated attendance record'
                ]);
                
                logger()->info("Created attendance for student ID: {$enrollment->user->id} in session ID: {$this->id}");
            }
        }
    }

    /**
     * Static method to check all incomplete sessions
     */
    public static function checkAllIncompleteSessions(): void
    {
        $incompleteSessions = CourseRecordSession::where('is_completed', false)->get();
        
        foreach ($incompleteSessions as $session) {
            $session->checkAndCreateAttendance();
        }
        
        logger()->info("Checked " . $incompleteSessions->count() . " incomplete sessions for attendance creation");
    }

    public function getModuleProgressesAndEnrollments()
    {
        return [
            'moduleProgresses.lessonProgresses',
            'courseEnrollmentSessions',
        ];
    }
}
