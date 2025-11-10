<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class TaskSubmission extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'files' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Task yang di-submit
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * User yang submit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Assignment terkait
     */
    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class);
    }

    /**
     * Reviewer/Admin yang review submission
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope for pending submissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved submissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected submissions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for revision requested submissions
     */
    public function scopeRevisionRequested($query)
    {
        return $query->where('status', 'revision_requested');
    }

    /**
     * Check if submission is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if submission is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if submission is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if revision is requested
     */
    public function isRevisionRequested(): bool
    {
        return $this->status === 'revision_requested';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'revision_requested' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_requested' => 'Perlu Revisi',
            default => $this->status,
        };
    }
}
