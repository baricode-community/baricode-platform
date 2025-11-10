<?php

namespace App\Models\Learning;

use App\Models\Learning\Enrollment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseModule;
use App\Models\Enrolment\CourseEnrollment;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function courseModules()
    {
        return $this->hasMany(CourseModule::class, 'course_id', 'id')->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id');
    }
}
