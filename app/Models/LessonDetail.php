<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseModule;
use App\Models\StudentNote;

class LessonDetail extends Model
{
    /** @use HasFactory<\Database\Factories\CourseModuleLessonFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function studentNotes()
    {
        return $this->hasMany(StudentNote::class, 'lesson_id');
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }
}
