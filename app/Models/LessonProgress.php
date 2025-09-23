<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LessonDetail;
use App\Models\ModuleProgress;


class LessonProgress extends Model
{
    protected $guarded = ['id'];
    protected $table = 'lesson_progresses';

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function moduleProgress()
    {
        return $this->belongsTo(ModuleProgress::class, 'module_progress_id', 'id');
    }

    public function lessonDetail()
    {
        return $this->belongsTo(LessonDetail::class, 'lesson_id', 'id');
    }

}
