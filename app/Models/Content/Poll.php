<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Auth\User;

class Poll extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'is_public',
        'user_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'user_id' => 'integer',
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            do {
            $id = Str::random(5);
            } while (self::where('id', $id)->exists());
            $model->id = $id;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function getAllVotes()
    {
        $options = $this->options()->with('votes')->get();
        $allVotes = collect();
        foreach ($options as $option) {
            $allVotes = $allVotes->merge($option->votes);
        }
        return $allVotes;
    }

    public function close()
    {
        $this->update(['status' => 'closed']);
    }

    public function open()
    {
        $this->update(['status' => 'open']);
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isPublic()
    {
        return $this->is_public;
    }

    public function makePublic()
    {
        return $this->update(['is_public' => true]);
    }

    public function makePrivate()
    {
        return $this->update(['is_public' => false]);
    }

    public function proyekBarengs()
    {
        return $this->belongsToMany(ProyekBareng::class, 'proyek_bareng_polls', 'poll_id', 'proyek_bareng_id')
                    ->withPivot('title', 'description')
                    ->withTimestamps();
    }
}
