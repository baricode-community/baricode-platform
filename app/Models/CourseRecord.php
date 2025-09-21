<?php

namespace App\Models;

use App\Observers\CourseRecordObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;
use App\Models\ModuleRecord;
use App\Models\CourseRecordSession;

#[ObservedBy(CourseRecordObserver::class)]
class CourseRecord extends Model
{
    /** @use HasFactory<\Database\Factories\CourseRecordFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function (CourseRecord $courseRecord) {
            $modules = $courseRecord->course->modules;

            foreach ($modules as $module) {
                $courseRecord->moduleRecords()->create([
                    'module_id' => $module->id,
                ]);
            }
        });
    }

    public function courseRecordSessions()
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

    public function moduleRecords()
    {
        return $this->hasMany(ModuleRecord::class);
    }
}
