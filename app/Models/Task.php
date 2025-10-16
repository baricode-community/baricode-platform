<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Task extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
