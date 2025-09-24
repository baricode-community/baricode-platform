<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\Course\LessonDetail;

class UserNote extends Model
{
    protected $guarded = [ 'id' ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
