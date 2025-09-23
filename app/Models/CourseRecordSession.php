<?php

namespace App\Models;

use App\Traits\CourseRecordSessionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRecordSession extends Model
{
    use HasFactory, CourseRecordSessionTrait;

    protected $guarded = ['id'];

    public function courseEnrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }
}