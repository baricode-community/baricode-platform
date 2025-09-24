<?php

namespace App\Models\Enrollment;

use App\Models\Course\CourseModuleLesson;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\LessonDetail;
use App\Models\ModuleProgress;


class EnrollmentLesson extends Model
{
    protected $guarded = ['id'];
    protected $table = 'enrollment_lessons';

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function enrollmentModule()
    {
        return $this->belongsTo(EnrollmentModule::class, 'enrollment_module_id', 'id');
    }

    public function enrollmentLesson()
    {
        return $this->belongsTo(CourseModuleLesson::class, 'enrollment_lesson_id', 'id');
    }

}
