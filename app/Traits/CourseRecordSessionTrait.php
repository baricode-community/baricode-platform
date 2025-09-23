<?php

namespace App\Traits;

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

    public function isTimeToStudy(): bool
    {
        $currentTime = now();
        $startTime = $this->start_time;
        $endTime = $this->end_time;

        return $currentTime->between($startTime, $endTime);
    }
}
