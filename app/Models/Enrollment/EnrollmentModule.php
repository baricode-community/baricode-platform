<?php

namespace App\Models\Enrollment;

use App\Models\Enrollment\EnrollmentLesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Course\CourseModule;
use App\Models\LessonProgress;

class EnrollmentModule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'enrollment_modules';

    protected static function booted()
    {
        // Skip auto-creation during testing
        if (app()->environment('testing')) {
            return;
        }
        
        static::created(function (EnrollmentModule $enrollmentModule) {
            $lessons = $enrollmentModule->enrollment->course->courseModules()->where('id', $enrollmentModule->module_id)->first()->courseModuleLessons;

            foreach ($lessons as $lesson) {
                $enrollmentModule->enrollmentLessons()->create([
                    'lesson_id' => $lesson->id,
                    'enrollment_module_id' => $enrollmentModule->id,
                ]);
            }
        });
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'id');
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id', 'id');
    }

    public function enrollmentLessons()
    {
        return $this->hasMany(EnrollmentLesson::class, 'enrollment_module_id', 'id');
    }
}
