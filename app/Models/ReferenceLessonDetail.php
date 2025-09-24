<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenceLessonDetail extends Model
{
    protected $guarded = ['id'];

    public function lessonDetail()
    {
        return $this->belongsTo(LessonDetail::class, 'lesson_detail_id');
    }
}
