<?php

namespace App\Models\Enrollment;

use App\Traits\CourseRecordSessionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseAttendance;

class EnrollmentSession extends Model
{
    use HasFactory, CourseRecordSessionTrait;

    protected $guarded = ['id'];

    public function courseEnrollment()
    {
        return $this->belongsTo(\App\Models\Enrollment\Enrollment::class, 'course_enrollment_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(CourseAttendance::class, 'course_record_session_id', 'id');
    }
}