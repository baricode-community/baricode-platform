<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use App\Models\LessonRecord;


class ModuleProgress extends Model
{
    protected $guarded = ['id'];
    protected $table = 'module_progresses';

     protected static function booted()
    {
        static::created(function (ModuleProgress $moduleProgress) {

            $lessons = $moduleProgress->module->lessonDetails;

            foreach ($lessons as $lesson) {
                $moduleProgress->lessonProgresses()->create([
                    'lesson_id' => $lesson->id,
                ]);
            }
        });
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class);
    }
}
