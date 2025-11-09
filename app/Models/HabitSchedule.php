<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitSchedule extends Model
{
    protected $fillable = [
        'habit_id',
        'day_of_week',
        'scheduled_time',
        'is_active',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Relation to Habit
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Habit::class, 'habit_id');
    }

    /**
     * Get formatted day name
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->scheduled_time->format('H:i');
    }
}
