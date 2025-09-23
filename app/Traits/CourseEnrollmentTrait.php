<?php

namespace App\Traits;

use App\Models\CourseEnrollment;

trait CourseEnrollmentTrait
{
    public function logCourseEnrollmentDetails(CourseEnrollment $courseEnrollment)
    {
        if (env('APP_ENV') === 'local') {
            logger()->debug('CourseEnrollment details' . $courseEnrollment->load([
                'moduleProgresses.lessonProgresses',
                'courseEnrollmentSessions',
            ])->toJson());
        }
    }
}
