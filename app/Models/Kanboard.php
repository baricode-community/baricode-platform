<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Kanboard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'board_id',
        'visibility',
        'owner_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($kanboard) {
            if (!$kanboard->board_id) {
                $kanboard->board_id = Str::random(8);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kanboard_users')
            ->withPivot(['role', 'status', 'invited_by', 'invited_at', 'joined_at', 'permissions'])
            ->withTimestamps();
    }

    public function kanboardUsers(): HasMany
    {
        return $this->hasMany(KanboardUser::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(KanboardCard::class);
    }

    public function activeCards(): HasMany
    {
        return $this->hasMany(KanboardCard::class)->where('is_archived', false);
    }

    public function todoCards(): HasMany
    {
        return $this->activeCards()->where('status', 'todo')->orderBy('order');
    }

    public function doingCards(): HasMany
    {
        return $this->activeCards()->where('status', 'doing')->orderBy('order');
    }

    public function doneCards(): HasMany
    {
        return $this->activeCards()->where('status', 'done')->orderBy('order');
    }

    public function managers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'manager')->wherePivot('status', 'active');
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member')->wherePivot('status', 'active');
    }

    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin')->wherePivot('status', 'active');
    }

    // Helper methods for permissions
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function isManager(User $user): bool
    {
        return $this->users()->wherePivot('user_id', $user->id)
            ->wherePivot('role', 'manager')
            ->wherePivot('status', 'active')
            ->exists();
    }

    public function isAdmin(User $user): bool
    {
        return $this->users()->wherePivot('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->wherePivot('status', 'active')
            ->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->users()->wherePivot('user_id', $user->id)
            ->wherePivot('status', 'active')
            ->exists();
    }

    public function canManage(User $user): bool
    {
        return $this->isOwner($user) || $this->isAdmin($user);
    }

    public function canAccess(User $user): bool
    {
        if ($this->visibility === 'public') {
            return true;
        }
        
        return $this->isOwner($user) || $this->isMember($user);
    }

    public function getRouteKeyName()
    {
        return 'board_id';
    }

    public function scopeAccessibleBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public')
              ->orWhere('owner_id', $user->id)
              ->orWhereHas('users', function ($subQ) use ($user) {
                  $subQ->where('user_id', $user->id)->where('status', 'active');
              });
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function proyekBarengs(): BelongsToMany
    {
        return $this->belongsToMany(ProyekBareng::class, 'proyek_bareng_kanboards', 'kanboard_id', 'proyek_bareng_id')
                    ->withPivot('description')
                    ->withTimestamps();
    }
}
