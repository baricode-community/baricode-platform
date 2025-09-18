<?php

namespace App\Services;

use App\Models\Course;

class CourseService
{
    public function startCourse(Course $course, $userId): bool
    {
        $context = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'user_id' => $userId,
        ];

        logger()->info('Starting course', $context);
        if (!$course->is_published) {
            logger()->warning('Attempt to start unpublished course', $context);
            flash()->warning('Kursus ini belum dipublikasikan.');
            return false;
        }

        // Mengecek limit peserta dan sebagainya
        // FIXME

        flash()->success('Anda telah berhasil memulai kursus: ' . $course->title);
        return true;
    }
}