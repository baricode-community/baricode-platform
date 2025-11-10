<?php

namespace App\Traits;

use App\Models\Learning\Enrollment;

trait EnrollmentTrait
{
    public function logCourseEnrollmentDetails(Enrollment $enrollment)
    {
        if (env('APP_ENV') === 'local') {
            logger()->debug('Enrollment details' . $enrollment->load([
                'user',
                'enrollmentModules.enrollmentModules.enrollmentSessions',
            ])->toJson());
        }
    }

    public function isWaktunyaBelajar(): bool
    {
        $now = now();
        return false;
    }
}
