<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function courseEnrollment()
    {
        return $this->belongsTo(\App\Models\Enrollment\Enrollment::class, 'course_enrollment_id', 'id');
    }
}