<?php

namespace App\Traits;

use App\Models\CourseEnrollment;

trait EnrollmentTrait
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

    public function isWaktunyaBelajar(): bool
    {
        $now = now();
        return false;
    }
}
