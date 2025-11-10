<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'log_date',
        'log_time',
        'status',
        'notes',
        'logged_at',
    ];

    protected $casts = [
        'log_date' => 'date',
        'log_time' => 'datetime:H:i',
        'logged_at' => 'datetime',
    ];

    /**
     * Relation to Habit
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Habits\Habit::class, 'habit_id');
    }

    /**
     * Relation to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class, 'user_id');
    }

    /**
     * Check if status is present
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Check if status is absent
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * Check if status is late
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        $statuses = [
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Check if this log can still be edited
     * Can be edited within 24 hours of creation
     */
    public function canBeEdited(): bool
    {
        return $this->created_at->diffInHours(now()) <= 24;
    }

    /**
     * Get remaining edit time in hours
     */
    public function getRemainingEditTimeAttribute(): int
    {
        $hoursElapsed = $this->created_at->diffInHours(now());
        return max(0, 24 - $hoursElapsed);
    }

    /**
     * Get formatted remaining edit time
     */
    public function getFormattedRemainingEditTimeAttribute(): string
    {
        $remainingHours = $this->remaining_edit_time;
        
        if ($remainingHours <= 0) {
            return 'Tidak dapat diedit lagi';
        }
        
        if ($remainingHours < 1) {
            $minutes = $this->created_at->diffInMinutes(now()->subHours(23));
            return "Tersisa {$minutes} menit";
        }
        
        return "Tersisa {$remainingHours} jam";
    }
}
