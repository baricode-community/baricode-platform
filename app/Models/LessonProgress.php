<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModuleRecord;
use App\Models\Lesson;

class LessonProgress extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function moduleProgress()
    {
        return $this->belongsTo(ModuleRecord::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

}
