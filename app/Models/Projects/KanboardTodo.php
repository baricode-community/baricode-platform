<?php

namespace App\Models\Projects;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanboardTodo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kanboard_card_id',
        'title',
        'description',
        'is_completed',
        'order',
        'due_date',
        'created_by',
        'assigned_to',
        'completed_by',
        'completed_at',
        'completion_notes',
        'priority',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(KanboardCard::class, 'kanboard_card_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kanboard_todo_users')
            ->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TodoMessage::class, 'kanboard_todo_id');
    }

    public function latestMessages(): HasMany
    {
        return $this->messages()->latest()->limit(10);
    }

    public function markAsCompleted(User $user, string $notes = null): void
    {
        $this->update([
            'is_completed' => true,
            'completed_by' => $user->id,
            'completed_at' => now(),
            'completion_notes' => $notes,
        ]);
    }

    public function markAsIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_by' => null,
            'completed_at' => null,
            'completion_notes' => null,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->is_completed;
    }

    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    public function isMediumPriority(): bool
    {
        return $this->priority === 'medium';
    }

    public function isLowPriority(): bool
    {
        return $this->priority === 'low';
    }

    public function canBeCompletedBy(User $user): bool
    {
        $kanboard = $this->card->kanboard;
        
        // Owner, admin, or manager can complete any todo
        if ($kanboard->canManage($user)) {
            return true;
        }
        
        // Any assigned user can complete the todo
        if ($this->assignedUsers()->where('users.id', $user->id)->exists()) {
            return true;
        }
        
        // Legacy: Assigned user can complete their own todo (for backward compatibility)
        if ($this->assigned_to === $user->id) {
            return true;
        }
        
        // Creator can complete their own todo
        if ($this->created_by === $user->id) {
            return true;
        }
        
        return false;
    }

    public function assignUser(User $user, User $assignedBy): void
    {
        // Debug: Check if user is already assigned
        $alreadyAssigned = $this->assignedUsers()->where('users.id', $user->id)->exists();
        
        if (!$alreadyAssigned) {
            $this->assignedUsers()->attach($user->id, [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy->id,
            ]);
            
            // Force refresh the relationship
            $this->load('assignedUsers');
        }
    }

    public function unassignUser(User $user): void
    {
        $this->assignedUsers()->detach($user->id);
    }

    public function assignUsers(array $userIds, User $assignedBy): void
    {
        $this->assignedUsers()->sync([]);
        
        foreach ($userIds as $userId) {
            $this->assignedUsers()->attach($userId, [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy->id,
            ]);
        }
    }

    public function isAssignedTo(User $user): bool
    {
        // Check many-to-many relationship first
        return $this->assignedUsers()->where('users.id', $user->id)->exists() || 
               $this->assigned_to === $user->id; // Legacy support
    }

    public function canSendMessage(User $user): bool
    {
        $kanboard = $this->card->kanboard;
        
        // Owner, admin, or manager can send messages
        if ($kanboard->canManage($user) || $kanboard->isManager($user)) {
            return true;
        }
        
        // Any assigned user can send messages
        if ($this->isAssignedTo($user)) {
            return true;
        }
        
        return false;
    }

    public function canViewMessages(User $user): bool
    {
        $kanboard = $this->card->kanboard;
        
        // Anyone who can access the kanboard can view messages
        return $kanboard->canAccess($user);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }

    public function scopeCreatedBy($query, User $user)
    {
        return $query->where('created_by', $user->id);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 'medium');
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', 'low');
    }
}
