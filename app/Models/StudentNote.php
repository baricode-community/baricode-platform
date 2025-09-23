<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\LessonDetail;

class StudentNote extends Model
{
    protected $guarded = [ 'id' ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lessonDetail()
    {
        return $this->belongsTo(LessonDetail::class, 'lesson_id');
    }
}
