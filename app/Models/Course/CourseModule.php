<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Course\Course;
use App\Models\Course\CourseModuleLesson;
use App\Models\ModuleProgress;

class CourseModule extends Model
{    
    /** @use HasFactory<\Database\Factories\CourseModuleFactory> */
    use HasFactory;
    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function courseModuleLessons()
    {
        return $this->hasMany(CourseModuleLesson::class, 'module_id', 'id')->orderBy('order');
    }
}
