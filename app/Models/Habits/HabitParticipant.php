<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitParticipant extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'status',
        'joined_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Relation to Habit
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Habits\Habit::class, 'habit_id');
    }

    /**
     * Relation to User (Participant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class, 'user_id');
    }

    /**
     * Relation to User (Approver)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class, 'approved_by');
    }

    /**
     * Approve the participant
     */
    public function approve($approverId = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approverId,
        ]);
    }

    /**
     * Reject the participant
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Check if participant is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if participant is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
