<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class PersonalFlashCard extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
