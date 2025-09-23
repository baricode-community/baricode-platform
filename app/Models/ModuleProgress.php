<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseModule;
use App\Models\LessonProgress;
use App\Models\CourseEnrollment;

class ModuleProgress extends Model
{
    protected $guarded = ['id'];
    protected $table = 'module_progresses';

     protected static function booted()
    {
        static::created(function (ModuleProgress $moduleProgress) {

                        $lessons = $moduleProgress->courseModule->lessonDetails;

            foreach ($lessons as $lesson) {
                $moduleProgress->lessonProgresses()->create([
                    'lesson_id' => $lesson->id,
                ]);
            }
        });
    }

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function courseEnrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class, 'module_progress_id', 'id');
    }
}
