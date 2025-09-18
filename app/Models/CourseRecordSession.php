<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRecordSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function courseRecord()
    {
        return $this->belongsTo(CourseRecord::class);
    }
}