<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class HabitInvitation extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'inviter_id',
        'token',
        'status',
        'expires_at',
        'responded_at',
        'message',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = static::generateUniqueToken();
            }
            
            if (empty($model->expires_at)) {
                $model->expires_at = now()->addDays(7); // expires in 7 days
            }
        });
    }

    /**
     * Generate unique token for invitation
     */
    protected static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('token', $token)->exists());

        return $token;
    }

    /**
     * Relation to Habit
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Habits\Habit::class, 'habit_id');
    }

    /**
     * Relation to User (Inviter)
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class, 'inviter_id');
    }

    /**
     * Relation to User (Invitee)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class, 'user_id');
    }
    
    /**
     * Accept the invitation
     */
    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        // Create participant record
        HabitParticipant::create([
            'habit_id' => $this->habit_id,
            'user_id' => $this->user_id, // User yang diundang
            'status' => 'approved',
            'joined_at' => now(),
            'approved_at' => now(),
            'approved_by' => $this->inviter_id,
        ]);
    }

    /**
     * Reject the invitation
     */
    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);
    }

    /**
     * Check if invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now() || $this->status === 'expired';
    }

    /**
     * Check if invitation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Mark as expired if past expiry date
     */
    public function checkExpiry(): void
    {
        if ($this->status === 'pending' && $this->expires_at < now()) {
            $this->update(['status' => 'expired']);
        }
    }
}
