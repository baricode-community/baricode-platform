<?php

namespace App\Models\Enrollment;

use App\Models\Enrollment\EnrollmentSession;
use App\Traits\CourseEnrollmentTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\Course\Course;


class Enrollment extends Model
{
    /** @use HasFactory<\Database\Factories\CourseEnrollmentFactory> */
    use HasFactory, CourseEnrollmentTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function (Enrollment $courseEnrollment) {
            $modules = $courseEnrollment->course->courseModules;

            foreach ($modules as $module) {
                $courseEnrollment->moduleProgresses()->create([
                    'module_id' => $module->id,
                ]);
            }
        });
    }

    public function enrolmentSessions()
    {
        return $this->hasMany(EnrollmentSession::class, 'course_enrollment_id', 'id')->orderBy('created_at', 'desc');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function moduleProgresses()
    {
        return $this->hasMany(ModuleProgress::class, 'course_enrollment_id', 'id');
    }

    public function courseRecordSessions()
    {
        return $this->hasMany(CourseRecordSession::class, 'course_enrollment_id', 'id');
    }
}
