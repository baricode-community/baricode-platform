<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User\User;

class Poll extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

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
}
