<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use App\Models\LessonRecord;

class ModuleRecord extends Model
{
    protected $guarded = ['id'];

     protected static function booted()
    {
        static::created(function (ModuleRecord $moduleRecord) {

            $lessons = $moduleRecord->module->lessons;

            foreach ($lessons as $lesson) {
                $moduleRecord->lessonRecords()->create([
                    'lesson_id' => $lesson->id,
                ]);
            }
        });
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function lessonRecords()
    {
        return $this->hasMany(LessonRecord::class);
    }
}
