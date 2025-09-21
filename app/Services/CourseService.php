<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseRecord;

class CourseService
{
    public function startCourse(Course $course): bool
    {
        $user = auth()->user();
        $context = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'user_id' => $user->id,
        ];

        logger()->info('Starting course', $context);
        if (!$course->is_published) {
            logger()->warning('Attempt to start unpublished course', $context);
            flash()->warning('Kursus ini belum dipublikasikan.');
            return false;
        }

        // Mengecek limit peserta dan sebagainya
        // FIXME
        $records = $user->courseRecords()->where([
            'course_id' => $course->id,
            'is_finished' => false,
        ])->get();
        if ($records->count() > 0) {
            logger()->warning('User already has an active course record', $context);
            flash()->warning('Anda sudah memiliki catatan kursus aktif untuk kursus ini.');
            return false;
        }

        flash()->success('Anda telah berhasil memulai kursus: ' . $course->title);
        return true;
    }

    public function markLessonAsLearned($lesson, $userId): void
    {
        $context = [
            'lesson_id' => $lesson->id,
            'lesson_title' => $lesson->title,
            'user_id' => $userId,
        ];

        logger()->info('Marking lesson as learned', $context);
        // Logika untuk menandai pelajaran sebagai telah dipelajari
        // FIXME

        flash()->success('Anda telah menandai pelajaran: ' . $lesson->title . ' sebagai telah dipelajari.');
    }
}