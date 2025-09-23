<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Course;
use App\Models\LessonDetail;

class CourseModule extends Model
{    
    /** @use HasFactory<\Database\Factories\CourseModuleFactory> */
    use HasFactory;
    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class)->orderBy('order');
    }

    public function lessonDetails()
    {
        return $this->hasMany(LessonDetail::class,  'module_id', 'id')->orderBy('order');
    }

    public function moduleProgresses()
    {
        return $this->hasMany(ModuleProgress::class, 'module_id');
    }
}
