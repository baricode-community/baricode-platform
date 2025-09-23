<?php

namespace App\Observers;

use App\Models\CourseRecord;
use App\Traits\CourseRecordTrait;

class CourseRecordObserver
{
    use CourseRecordTrait;

    /**
     * Handle the CourseRecord "created" event.
     */
    public function created(CourseRecord $courseRecord): void
    {
        logger()->info('CourseRecord created', ['id' => $courseRecord->id]);
        $this->logCourseRecordDetails($courseRecord);
    }

    /**
     * Handle the CourseRecord "updated" event.
     */
    public function updated(CourseRecord $courseRecord): void
    {
        logger()->info('CourseRecord updated', ['id' => $courseRecord->id]);
    }

    /**
     * Handle the CourseRecord "deleted" event.
     */
    public function deleted(CourseRecord $courseRecord): void
    {
        logger()->info('CourseRecord deleted', ['id' => $courseRecord->id]);
    }

    /**
     * Handle the CourseRecord "restored" event.
     */
    public function restored(CourseRecord $courseRecord): void
    {
        //
    }

    /**
     * Handle the CourseRecord "force deleted" event.
     */
    public function forceDeleted(CourseRecord $courseRecord): void
    {
        //
    }
}
