<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Learning\Course;

class CourseCategory extends Model
{
    /** @use HasFactory<\Database\Factories\Course\CourseCategoryFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id', 'id');
    }
}
