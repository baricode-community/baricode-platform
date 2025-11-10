<?php

namespace App\Models\Tracking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeTrackerEntry extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'started_at',
        'stopped_at',
        'duration',
        'is_running',
        'note',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
        'is_running' => 'boolean',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(TimeTrackerTask::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class);
    }

    public function stop(): void
    {
        $this->stopped_at = now();
        $this->is_running = false;
        
        // Calculate duration
        $this->duration = $this->stopped_at->diffInSeconds($this->started_at);
        $this->save();
    }

    public function getCurrentDuration(): int
    {
        if ($this->is_running) {
            return abs(now()->diffInSeconds($this->started_at));
        }
        
        return abs($this->duration);
    }

    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->getCurrentDuration();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
