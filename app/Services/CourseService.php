<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseRecord;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function startCourse(Course $course): CourseRecord | null
    {
        $user = auth()->user();
        $context = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'user_id' => $user->id,
        ];

        logger()->info('Starting course', $context);
        if (! $course->is_published) {
            logger()->warning('Attempt to start unpublished course', $context);
            flash()->warning('Kursus ini belum dipublikasikan.');

            return null;
        }

        $records = $user->courseRecords()->where([
            'course_id' => $course->id,
            'is_finished' => false,
        ])->get();
        if ($records->count() > 3) {
            logger()->warning('User already has an active course record', $context);
            flash()->warning('Anda sudah memiliki kursus aktif sebanyak 3 untuk saat ini. Selesaikan salah satu kursus sebelum memulai yang baru.');

            return null;
        }

        DB::beginTransaction();
        $result = null;
        try {
            $course = CourseRecord::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $result = $course;
        } catch (\Exception $e) {
            logger()->error('Failed to start course', array_merge($context, ['error' => $e->getMessage()]));
            DB::rollBack();
            flash()->error('Gagal memulai kursus. Silakan coba lagi.');

            return null;
        }
        DB::commit();

        flash()->success('Anda telah berhasil memulai kursus: '.$course->title);

        return $result;
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

        flash()->success('Anda telah menandai pelajaran: '.$lesson->title.' sebagai telah dipelajari.');
    }
}
