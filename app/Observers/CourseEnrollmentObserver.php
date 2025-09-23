<?php

namespace App\Observers;

use App\Models\CourseEnrollment;
use App\Traits\CourseEnrollmentTrait;

class CourseEnrollmentObserver
{
    use CourseEnrollmentTrait;

    /**
     * Handle the CourseRecord "created" event.
     */
    public function created(CourseEnrollment $courseEnrollment): void
    {
        logger()->info('CourseEnrollment created', ['id' => $courseEnrollment->id]);
        $this->logCourseEnrollmentDetails($courseEnrollment);
    }

    /**
     * Handle the CourseRecord "updated" event.
     */
    public function updated(CourseEnrollment $courseEnrollment): void
    {
        logger()->info('CourseEnrollment updated', ['id' => $courseEnrollment->id]);
    }

    /**
     * Handle the CourseRecord "deleted" event.
     */
    public function deleted(CourseEnrollment $courseEnrollment): void
    {
        logger()->info('CourseEnrollment deleted', ['id' => $courseEnrollment->id]);
    }

    /**
     * Handle the CourseRecord "restored" event.
     */
    public function restored(CourseEnrollment $courseEnrollment): void
    {
        //
    }

    /**
     * Handle the CourseRecord "force deleted" event.
     */
    public function forceDeleted(CourseEnrollment $courseEnrollment): void
    {
        //
    }
}
