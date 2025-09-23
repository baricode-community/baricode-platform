<?php

namespace App\Models;

use App\Observers\CourseEnrollmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;
use App\Models\ModuleProgress;
use App\Models\CourseRecordSession;

#[ObservedBy(CourseEnrollmentObserver::class)]
class CourseEnrollment extends Model
{
    /** @use HasFactory<\Database\Factories\CourseEnrollmentFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function (CourseEnrollment $courseEnrollment) {
            $modules = $courseEnrollment->course->modules;

            foreach ($modules as $module) {
                $courseEnrollment->moduleProgresses()->create([
                    'module_id' => $module->id,
                ]);
            }
        });
    }

    public function courseEnrollmentSessions()
    {
        return $this->hasMany(CourseRecordSession::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moduleProgresses()
    {
        return $this->hasMany(ModuleProgress::class);
    }
}
