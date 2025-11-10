<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class PersonalTube extends Model
{
    protected $table = 'personal_tubes';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
