<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function startCourse(Course $course, array $request): CourseEnrollment | null
    {
        $user = auth()->user();
        if (!$user) {
            logger()->warning('No authenticated user found when starting course', [
                'course_id' => $course->id,
                'course_title' => $course->title,
            ]);
            flash()->warning('Anda harus login untuk memulai kursus.');
            return null;
        }
        // Pastikan $request selalu array
        if (!is_array($request)) {
            $request = [];
        }
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

        $records = $user->courseEnrollments()->where([
            'course_id' => $course->id,
        ])->get();
        logger()->info('Current active course enrollments', array_merge($context, ['active_enrollments_count' => $records->count()]));
        
        if ($records->count() > 3) {
            logger()->warning('User already has an active course enrollment', $context);
            flash()->warning('Anda sudah memiliki kursus aktif sebanyak 3 untuk saat ini. Selesaikan salah satu kursus sebelum memulai yang baru.');

            return null;
        }

        DB::beginTransaction();
        $result = null;
        try {
            logger()->info('Creating course enrollment', $context);

            // Membuat course enrollment
            $courseEnrollment = CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $result = $courseEnrollment;
            logger()->info('Course enrollment created', array_merge($context, ['course_enrollment_id' => $courseEnrollment->id]));

            // Membuat sessions
               
            $days = $request['days'] ?? [];
            if (!is_array($days)) {
                $days = [];
            }
            logger()->info('Days for sessions', array_merge($context, ['days' => $days]));

            foreach ($days as $dayName => $day) {
                $dayNumber = match ($dayName) {
                    'Minggu'   => 1,
                    'Senin'  => 2,
                    'Selasa' => 3,
                    'Rabu'   => 4,
                    'Kamis'  => 5,
                    'Jumat'  => 6,
                    'Sabtu'  => 7
                };

                $courseEnrollment->courseEnrollmentSessions()->create([
                    'user_id' => $user->id,
                    'day_of_week' => $dayNumber,
                    'reminder_1' => $day['sesi_1'] ?? '07:00',
                    'reminder_2' => $day['sesi_2'] ?? null,
                    'reminder_3' => $day['sesi_3'] ?? null,
                ]);
            }
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

        flash()->success('Anda telah menandai pelajaran: '.$lesson->title.' sebagai telah dipelajari.');
    }
}
