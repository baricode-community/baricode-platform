<?php

namespace App\Traits;

use App\Models\CourseRecordSession;
use App\Models\CourseAttendance;
use App\Services\WhatsAppService;
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
        $currentTime = now('Asia/Jakarta');
        $currentDayOfWeek = $currentTime->dayOfWeek == 0 ? 1 : $currentTime->dayOfWeek + 1; // 0 (Sun) => 1 (Ahad), 1-6 => 2-7
        logger()->info("Checking session ID: {$this->id} for attendance creation at " . $currentTime->toDateTimeString());
        
        // Check if today matches the session day
        if ($this->day_of_week !== $currentDayOfWeek) {
            return false;
        }
        
        $reminders = [$this->reminder_1, $this->reminder_2, $this->reminder_3];
        
        // Check each reminder time
        foreach ($reminders as $reminder) {
            if (empty($reminder)) continue;
            
            try {
                // Handle both H:i and H:i:s format
                $reminderTimeString = is_string($reminder) ? $reminder : $reminder->format('H:i:s');
                $reminderTime = Carbon::createFromFormat('H:i:s', $reminderTimeString);
                
                // Create datetime objects for comparison
                $reminderDateTime = $currentTime->copy()->setTime($reminderTime->hour, $reminderTime->minute, $reminderTime->second);
                $reminderEndTime = $reminderDateTime->copy()->addMinutes(5);
                
                // Check if current time is within the reminder time range (reminder time to +5 minutes)
                if ($currentTime->between($reminderDateTime, $reminderEndTime)) {
                    // Get the student from this specific session's enrollment
                    $student = $this->courseEnrollment->user;
                    
                    // Check if attendance already exists for this session, student, and reminder time
                    $existingAttendance = CourseAttendance::where('course_record_session_id', $this->id)
                        ->where('student_id', $student->id)
                        ->where('waktu_absensi', $reminderTimeString)
                        ->first();
                    
                    if (!$existingAttendance) {
                        // Create attendance record
                        CourseAttendance::create([
                            'course_id' => $this->courseEnrollment->course->id,
                            'course_record_session_id' => $this->id,
                            'student_id' => $student->id,
                            'status' => CourseAttendance::STATUS_BELUM,
                            'absent_date' => now()->format('Y-m-d'),
                            'waktu_absensi' => $reminderTimeString,
                            'notes' => 'Auto-generated attendance record for reminder at ' . $reminderTimeString,
                            'created_at' => $reminderDateTime, // Set created_at to the reminder time
                        ]);
                        
                        logger()->info("Created attendance for student ID: {$student->id} in session ID: {$this->id} for reminder time: {$reminderTimeString}");
                        
                        // Send WhatsApp notification
                        $this->sendWhatsAppNotification($student, $reminderTimeString);
                        
                        return true;
                    }
                }
            } catch (\Exception $e) {
                logger()->warning("Failed to parse reminder time: {$reminder}. Error: " . $e->getMessage());
                continue;
            }
        }
        
        return false;
    }

    /**
     * Send WhatsApp notification to student
     */
    private function sendWhatsAppNotification($student, $reminderTime): void
    {
        try {
            $whatsappService = new WhatsAppService();
            $courseName = $this->courseEnrollment->course->name ?? 'Course';
            $studentName = $student->name ?? 'Student';
            $phoneNumber = $student->whatsapp ?? $student->phone;
            
            if ($phoneNumber) {
                // Format phone number (ensure it starts with country code)
                $phoneNumber = $this->formatPhoneNumber($phoneNumber);
                
                $success = $whatsappService->sendAttendanceReminder(
                    $phoneNumber,
                    $studentName, 
                    $courseName,
                    $reminderTime
                );
                
                if ($success) {
                    logger()->info("WhatsApp notification sent successfully to {$phoneNumber} for student ID: {$student->id}");
                } else {
                    logger()->error("Failed to send WhatsApp notification to {$phoneNumber} for student ID: {$student->id}");
                }
            } else {
                logger()->warning("No phone number found for student ID: {$student->id}");
            }
        } catch (\Exception $e) {
            logger()->error("Error sending WhatsApp notification: " . $e->getMessage());
        }
    }

    /**
     * Format phone number to include country code
     */
    private function formatPhoneNumber($phoneNumber): string
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with +62
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }
        
        // If doesn't start with 62, add 62
        if (substr($phoneNumber, 0, 2) !== '62') {
            $phoneNumber = '62' . $phoneNumber;
        }
        
        return $phoneNumber;
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
