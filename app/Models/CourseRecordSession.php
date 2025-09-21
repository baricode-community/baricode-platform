<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRecordSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getHari()
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

    public function courseRecord()
    {
        return $this->belongsTo(CourseRecord::class);
    }
}