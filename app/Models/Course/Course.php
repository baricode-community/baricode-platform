<?php

namespace App\Models\Course;

use App\Models\Enrollment\Enrollment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Course\CourseCategory;
use App\Models\Course\CourseModule;
use App\Models\Enrolment\CourseEnrollment;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id', 'id');
    }

    public function courseModules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
