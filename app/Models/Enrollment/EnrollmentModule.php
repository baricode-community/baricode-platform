<?php

namespace App\Models\Enrollment;

use App\Models\Enrollment\EnrollmentLesson;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\CourseModule;
use App\Models\LessonProgress;

class EnrollmentModule extends Model
{
    protected $guarded = ['id'];
    protected $table = 'enrollment_modules';

     protected static function booted()
    {
        static::created(function (EnrollmentModule $enrollmentModule) {
            $lessons = $enrollmentModule->courseModule->courseModuleLessons()->sortBy('order');

            foreach ($lessons as $lesson) {
                $enrollmentModule->enrollmentLessons()->create([
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
        return $this->belongsTo(\App\Models\Enrollment\Enrollment::class, 'enrollment_module_id', 'id');
    }

    public function enrollmentLessons()
    {
        return $this->hasMany(EnrollmentLesson::class, 'enrollment_module_id', 'id');
    }
}
