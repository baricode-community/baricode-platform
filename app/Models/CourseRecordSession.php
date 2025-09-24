<?php

namespace App\Models;

use App\Observers\CourseRecordSessionObserver;
use App\Traits\CourseRecordSessionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseEnrollment;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Models\CourseAttendance;

#[ObservedBy(CourseRecordSessionObserver::class)]
class CourseRecordSession extends Model
{
    use HasFactory, CourseRecordSessionTrait;

    protected $guarded = ['id'];

    public function courseEnrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function attendances()
    {
        return $this->hasMany(CourseAttendance::class, 'course_record_session_id', 'id');
    }
}