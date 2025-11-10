<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Auth\User;

class PollVote extends Model
{
    protected $fillable = [
        'poll_option_id',
        'user_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = Str::random(5);
        });
    }

    public function pollOption()
    {
        return $this->belongsTo(PollOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
