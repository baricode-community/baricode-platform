<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Learning\CourseModule;
use App\Models\User\UserNote;
use App\Models\LessonProgress;
use App\Models\ReferenceLessonDetail;

class CourseModuleLesson extends Model
{
    /** @use HasFactory<\Database\Factories\CourseModuleLessonFactory> */
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'course_module_lessons';

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class, 'module_id', 'id');
    }

    public function userNotes()
    {
        if (auth()->check()) {
            return $this->hasMany(UserNote::class, 'lesson_id', 'id')
                ->where('user_id', auth()->id());
        }
        return $this->hasMany(UserNote::class, 'lesson_id', 'id');
    }
}
