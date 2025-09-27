<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User\User;

class Meet extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'title',
        'youtube_link',
        'description',
        'scheduled_at',
        'is_finished'
    ];
    
    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_finished' => 'boolean',
    ];
    
    /**
     * Get the users that belong to the meet.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'meet_user')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }
    
    /**
     * Get the count of participants
     */
    public function participantsCount()
    {
        return $this->users()->count();
    }
    
    /**
     * Check if user is participant
     */
    public function isParticipant(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
