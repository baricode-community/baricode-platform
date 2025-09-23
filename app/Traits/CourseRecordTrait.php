<?php

namespace App\Traits;

use App\Models\CourseRecord;

trait CourseRecordTrait
{
    public function logCourseRecordDetails(CourseRecord $courseRecord)
    {
        if (env('APP_ENV') === 'local') {
            logger()->debug('CourseRecord details' . $courseRecord->load([
                'moduleRecords.lessonRecords',
                'courseRecordSessions',
            ])->toJson());
        }
    }
}
