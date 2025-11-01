<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanboardCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kanboard_id',
        'title',
        'description',
        'status',
        'order',
        'color',
        'labels',
        'due_date',
        'created_by',
        'assigned_to',
        'is_archived',
    ];

    protected $casts = [
        'labels' => 'array',
        'due_date' => 'datetime',
        'is_archived' => 'boolean',
        'kanboard_id' => 'integer',
    ];

    public function kanboard(): BelongsTo
    {
        return $this->belongsTo(Kanboard::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function todos(): HasMany
    {
        return $this->hasMany(KanboardTodo::class);
    }

    public function activeTodos(): HasMany
    {
        return $this->todos()->whereNull('deleted_at')->orderBy('order');
    }

    public function completedTodos(): HasMany
    {
        return $this->todos()->where('is_completed', true);
    }

    public function pendingTodos(): HasMany
    {
        return $this->todos()->where('is_completed', false);
    }

    public function moveToStatus(string $status): void
    {
        $this->update(['status' => $status]);
    }

    public function archive(): void
    {
        $this->update(['is_archived' => true]);
    }

    public function restore(): void
    {
        $this->update(['is_archived' => false]);
    }

    public function isTodo(): bool
    {
        return $this->status === 'todo';
    }

    public function isDoing(): bool
    {
        return $this->status === 'doing';
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isDone();
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }

    public function scopeCreatedBy($query, User $user)
    {
        return $query->where('created_by', $user->id);
    }
}