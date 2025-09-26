<?php

namespace App\Traits;

use App\Models\Course\Course;
use App\Models\Enrollment\Enrollment;
use Illuminate\Support\Facades\DB;

trait CourseTrait
{
    public function startCourse(Course $course, array $request): Enrollment | null
    {
        $user = auth()->user();
        if (!$user) {
            logger()->warning('No user. Course: '.$course->title);
            flash()->warning('Anda harus login untuk memulai kursus.');
            return null;
        }
        // Pastikan $request selalu array
        if (!is_array($request)) {
            $request = [];
        }
        // Pastikan tidak ada course enrollment yang belum disetujui, jadi tidak boleh duplikat
        if ($user->courseEnrollments()->where(['course_id' => $course->id, 'is_approved' => false])->exists()) {
            logger()->warning('User already enrolled. Course: '.$course->title);
            flash()->warning('Anda sudah memulai di kursus ini. Silahkan selesaikan kursus tersebut terlebih dahulu.');

            return null;
        }

        logger()->info('Starting course: '.$course->title);
        if (! $course->is_published) {
            logger()->warning('Unpublished course: '.$course->title);
            flash()->warning('Kursus ini belum dipublikasikan.');

            return null;
        }

        $records = $user->courseEnrollments()->where([
            'course_id' => $course->id,
        ])->get();
        logger()->info('Enrollments count: '.$records->count().' Course: '.$course->title);
        
        if ($records->count() > 3) {
            logger()->warning('Max enrollments reached. Course: '.$course->title);
            flash()->warning('Anda sudah memiliki kursus aktif sebanyak 3 untuk saat ini. Selesaikan salah satu kursus sebelum memulai yang baru.');

            return null;
        }

        DB::beginTransaction();
        $result = null;
        try {
            logger()->info('Creating enrollment. Course: '.$course->title);

            // Membuat course enrollment
            $courseEnrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $result = $courseEnrollment;
            logger()->info('Enrollment created. Course: '.$course->title.' Enrollment ID: '.$courseEnrollment->id);

            // Membuat sessions
               
            $days = $request['days'] ?? [];
            if (!is_array($days)) {
                $days = [];
            }
            logger()->info('Sessions days: '.json_encode($days).'. Course: '.$course->title);

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

                $courseEnrollment->enrollmentSessions()->create([
                    'user_id' => $user->id,
                    'day_of_week' => $dayNumber,
                    'reminder_1' => $day['sesi_1'] ?? '07:00',
                    'reminder_2' => $day['sesi_2'] ?? null,
                    'reminder_3' => $day['sesi_3'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to start course: '.$course->title.' Error: '.$e->getMessage());
            DB::rollBack();
            flash()->error('Gagal memulai kursus. Silakan coba lagi.');

            return null;
        }
        DB::commit();

        flash()->success('Anda telah berhasil memulai kursus: '.$course->title);

        return $result;
    }
}
