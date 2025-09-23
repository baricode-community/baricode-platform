<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Course;
use App\Models\CourseModuleLesson;

class CourseModule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasMany(CourseModuleLesson::class)->orderBy('order');
    }
}
