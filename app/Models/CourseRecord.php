<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRecord extends Model
{
    /** @use HasFactory<\Database\Factories\CourseRecordFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
