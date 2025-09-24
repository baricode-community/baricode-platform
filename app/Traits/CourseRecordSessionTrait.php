<?php

namespace App\Traits;

use App\Models\CourseRecordSession;

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

    public function isTimeToStudy(CourseRecordSession $session): bool
    {
        $currentTime = now();
        $reminder1 = $this->reminder1;
        $reminder2 = $this->reminder2;
        $reminder3 = $this->reminder3;
        logger()->info("Current Time: {$currentTime->format('H:i')}");
        logger()->info("Reminders: {$reminder1}, {$reminder2}, {$reminder3}");

        // Tolong cek apakah waktu saat ini berada di antara salah satu dari tiga waktu pengingat
        foreach ([$reminder1, $reminder2, $reminder3] as $reminder) {
            if ($reminder && $currentTime->format('H:i') === $reminder) {
                logger()->info("It's time to study for session ID: {$session->id}");
                return true;
            }
        }
    
        logger()->info("Not time to study for session ID: {$session->id}");
        return false;
    }

    public function getModuleProgressesAndEnrollments()
    {
        return [
            'moduleProgresses.lessonProgresses',
            'courseEnrollmentSessions',
        ];
    }
}
