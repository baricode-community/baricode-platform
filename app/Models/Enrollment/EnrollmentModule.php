<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course\CourseModule;
use App\Models\LessonProgress;

class EnrollmentModule extends Model
{
    protected $guarded = ['id'];
    protected $table = 'module_progresses';

     protected static function booted()
    {
        static::created(function (EnrollmentModule $moduleProgress) {

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
        return $this->belongsTo(CourseModule::class, 'module_id', 'id');
    }

    public function courseEnrollment()
    {
        return $this->belongsTo(\App\Models\Enrollment\Enrollment::class, 'course_enrollment_id', 'id');
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class, 'module_progress_id', 'id');
    }
}
