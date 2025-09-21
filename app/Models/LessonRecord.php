<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModuleRecord;
use App\Models\Lesson;

class LessonRecord extends Model
{
    protected $guarded = ['id'];

    public function moduleRecord()
    {
        return $this->belongsTo(ModuleRecord::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

}
