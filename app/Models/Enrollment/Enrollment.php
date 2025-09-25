<?php

namespace App\Models\Enrollment;

use App\Models\Enrollment\EnrollmentSession;
use App\Models\Enrollment\EnrollmentModule;
use App\Traits\EnrollmentTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\Course\Course;


class Enrollment extends Model
{
    /** @use HasFactory<\Database\Factories\CourseEnrollmentFactory> */
    use HasFactory, EnrollmentTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];
    protected $table = 'enrollments';

    protected static function booted()
    {
        // Skip auto-creation during testing
        if (app()->environment('testing')) {
            return;
        }
        
        static::created(function (Enrollment $courseEnrollment) {
            $modules = $courseEnrollment->course->courseModules;

            foreach ($modules as $module) {
                $courseEnrollment->enrollmentModules()->create([
                    'module_id' => $module->id,
                ]);
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function enrollmentModules()
    {
        return $this->hasMany(EnrollmentModule::class, 'enrollment_id', 'id');
    }

    public function enrollmentSessions()
    {
        return $this->hasMany(EnrollmentSession::class, 'enrollment_id', 'id');
    }
}
