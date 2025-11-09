<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Habit extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'user_id',
        'duration_days',
        'start_date',
        'end_date',
        'is_active',
        'is_locked',
        'settings',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = static::generateUniqueId();
            }
            
            // Calculate end_date based on start_date and duration_days
            if ($model->start_date && $model->duration_days) {
                $durationDays = (int) $model->duration_days;
                $model->end_date = $model->start_date->copy()->addDays($durationDays);
            }
        });
    }

    /**
     * Generate unique 5-character ID
     */
    protected static function generateUniqueId(): string
    {
        do {
            $id = Str::upper(Str::random(5));
        } while (static::where('id', $id)->exists());

        return $id;
    }

    /**
     * Relation to User (Creator)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class, 'user_id');
    }

    /**
     * Relation to HabitSchedule
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(\App\Models\HabitSchedule::class);
    }

    /**
     * Relation to HabitParticipant
     */
    public function participants(): HasMany
    {
        return $this->hasMany(\App\Models\HabitParticipant::class);
    }

    /**
     * Relation to approved participants only
     */
    public function approvedParticipants(): HasMany
    {
        return $this->hasMany(\App\Models\HabitParticipant::class)->where('status', 'approved');
    }

    /**
     * Relation to HabitLog
     */
    public function logs(): HasMany
    {
        return $this->hasMany(\App\Models\HabitLog::class);
    }

    /**
     * Relation to HabitInvitation
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(\App\Models\HabitInvitation::class);
    }

    /**
     * Check if habit is still active
     */
    public function isActive(): bool
    {
        return $this->is_active && 
               $this->start_date <= now()->toDateString() && 
               $this->end_date >= now()->toDateString();
    }

    /**
     * Lock the habit so it cannot be modified
     */
    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Check if user is a participant of this habit
     */
    public function hasParticipant($userId): bool
    {
        return $this->participants()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get remaining days
     */
    public function remainingDays(): int
    {
        if ($this->end_date < now()->toDateString()) {
            return 0;
        }
        
        return now()->diffInDays($this->end_date);
    }
}
