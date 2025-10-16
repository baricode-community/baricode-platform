<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Task extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'attachments' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * User yang membuat task (creator/admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Semua assignment untuk task ini
     */
    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    /**
     * User-user yang di-assign ke task ini
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments')
            ->withPivot(['assigned_by', 'assigned_at', 'due_date', 'status', 'notes'])
            ->withTimestamps();
    }

    /**
     * Semua submissions untuk task ini
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    /**
     * Submissions yang sudah di-approve
     */
    public function approvedSubmissions()
    {
        return $this->hasMany(TaskSubmission::class)->where('status', 'approved');
    }

    /**
     * Submissions yang masih pending
     */
    public function pendingSubmissions()
    {
        return $this->hasMany(TaskSubmission::class)->where('status', 'pending');
    }

    /**
     * Check if user is assigned to this task
     */
    public function isAssignedTo(User $user): bool
    {
        return $this->assignments()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's assignments for this task
     */
    public function userAssignments(User $user)
    {
        return $this->assignments()->where('user_id', $user->id);
    }

    /**
     * Get user's submissions for this task
     */
    public function userSubmissions(User $user)
    {
        return $this->submissions()->where('user_id', $user->id);
    }

    /**
     * Check if user can still submit (based on max_submissions_per_user)
     * Now checks across all assignments for this user
     */
    public function userCanSubmit(User $user): bool
    {
        if (!$this->isAssignedTo($user)) {
            return false;
        }

        // Count total submissions across all assignments
        $submissionCount = $this->userSubmissions($user)->count();
        
        // Count total assignments for this user on this task
        $assignmentCount = $this->userAssignments($user)->count();
        
        // Total allowed submissions = max_submissions_per_user * assignment count
        $maxAllowedSubmissions = $this->max_submissions_per_user * $assignmentCount;
        
        return $submissionCount < $maxAllowedSubmissions;
    }

    /**
     * Check if specific assignment can still receive submission
     */
    public function assignmentCanSubmit(TaskAssignment $assignment): bool
    {
        $submissionCount = $assignment->submissions()->count();
        return $submissionCount < $this->max_submissions_per_user;
    }
}
