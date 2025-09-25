<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\CourseModule;
use App\Models\User\UserNote;
use App\Models\LessonProgress;
use App\Models\ReferenceLessonDetail;

class CourseModuleLesson extends Model
{
    /** @use HasFactory<\Database\Factories\CourseModuleLessonFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'module_id', 'id');
    }

    public function userNotes()
    {
        return $this->hasMany(UserNote::class, 'lesson_id', 'id');
    }
}
