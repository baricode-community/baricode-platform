<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeTrackerProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TimeTrackerTask::class, 'project_id');
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->tasks()
            ->with('entries')
            ->get()
            ->sum(function ($task) {
                return $task->entries->sum('duration');
            });
    }

    public function getFormattedTotalDurationAttribute(): string
    {
        $seconds = $this->total_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function canBeCompleted(): bool
    {
        // Check if all tasks are completed
        return $this->tasks()->where('is_completed', false)->count() === 0;
    }

    public function toggleCompletion(): array
    {
        if (!$this->is_completed && !$this->canBeCompleted()) {
            return [
                'success' => false,
                'message' => 'Cannot mark project as completed. Please complete all tasks first.',
            ];
        }

        $this->is_completed = !$this->is_completed;
        $this->completed_at = $this->is_completed ? now() : null;
        $this->save();

        return [
            'success' => true,
            'message' => $this->is_completed ? 'Project marked as completed.' : 'Project marked as incomplete.',
        ];
    }
}
