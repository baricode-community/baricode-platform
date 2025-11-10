<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class TaskAssignment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    /**
     * Task yang di-assign
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * User yang menerima assignment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User yang melakukan assignment (admin)
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Submissions untuk assignment ini
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'assignment_id');
    }

    /**
     * Latest submission
     */
    public function latestSubmission()
    {
        return $this->hasOne(TaskSubmission::class, 'assignment_id')->latestOfMany();
    }

    /**
     * Check if assignment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Scope for pending assignments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in progress assignments
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
