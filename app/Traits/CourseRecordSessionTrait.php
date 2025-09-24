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
        return true;
    }

    public function getModuleProgressesAndEnrollments()
    {
        return [
            'moduleProgresses.lessonProgresses',
            'courseEnrollmentSessions',
        ];
    }
}
