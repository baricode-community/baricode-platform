<?php

namespace App\Models\Tracking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeTrackerTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'description',
        'estimated_duration',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'estimated_duration' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(TimeTrackerProject::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TimeTrackerEntry::class, 'task_id');
    }

    public function activeEntry(): HasOne
    {
        return $this->hasOne(TimeTrackerEntry::class, 'task_id')->where('is_running', true);
    }

    public function getTotalDurationAttribute(): int
    {
        return abs($this->entries()->sum('duration'));
    }

    public function getFormattedTotalDurationAttribute(): string
    {
        $seconds = $this->total_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function getFormattedEstimatedDurationAttribute(): ?string
    {
        if (!$this->estimated_duration) {
            return null;
        }

        $seconds = $this->estimated_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function isOverEstimate(): bool
    {
        if (!$this->estimated_duration) {
            return false;
        }

        return $this->total_duration > $this->estimated_duration;
    }

    public function toggleCompletion(): void
    {
        $this->is_completed = !$this->is_completed;
        $this->completed_at = $this->is_completed ? now() : null;
        $this->save();
    }
}
