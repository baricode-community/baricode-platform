<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use App\Models\CourseCategory;
use App\Models\Module;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function courseModules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function courseRecords()
    {
        return $this->hasMany(CourseRecord::class);
    }
}
