<?php

namespace App\Services;

use App\Http\Requests\CourseStartRequest;
use App\Models\Course;
use App\Models\CourseRecord;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function startCourse(Course $course, array $request): CourseRecord | null
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
            // Membuat course record
            $courseRecord = CourseRecord::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $result = $courseRecord;

            // Membuat sessions
               
            $days = $request['days'] ?? [];

            foreach ($days as $dayName => $day) {
                switch ($dayName) {
                    case 'Ahad':
                        $dayNumber = 1;
                        break;
                    case 'Senin':
                        $dayNumber = 2;
                        break;
                    case 'Selasa':
                        $dayNumber = 3;
                        break;
                    case 'Rabu':
                        $dayNumber = 4;
                        break;
                    case 'Kamis':
                        $dayNumber = 5;
                        break;
                    case 'Jumat':
                        $dayNumber = 6;
                        break;
                    case 'Sabtu':
                        $dayNumber = 7;
                        break;
                    default:
                        $dayNumber = null;
                        break;
                }

                $courseRecord->courseRecordSessions()->create([
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
        // Logika untuk menandai pelajaran sebagai telah dipelajari
        // FIXME

        flash()->success('Anda telah menandai pelajaran: '.$lesson->title.' sebagai telah dipelajari.');
    }
}
