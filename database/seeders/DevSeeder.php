<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\User;
use App\Models\Course\CourseCategory;
use App\Models\Course\Course;
use App\Models\Course\CourseModule;
use App\Models\Course\LessonDetail;

class DevSeeder extends Seeder
{
    /**
     * Seed the application's database for development.
     */
    public function run(): void
    {
        // Membuat 10 pengguna (user) acak.
        User::factory(10)->create();

        // Membuat 5 kategori, dan untuk setiap kategori:
        CourseCategory::factory(5)->create()->each(function ($category) {
            // Membuat 3 hingga 7 course (kursus) untuk kategori ini.
            Course::factory(rand(3, 7))->create([
                'category_id' => $category->id,
            ])->each(function ($course) {
                // Membuat 4 hingga 15 module (modul) untuk setiap course.
                $modules = CourseModule::factory(rand(4, 15))->make();

                // Menyimpan modules ke dalam course dan mengurutkannya.
                foreach ($modules as $index => $module) {
                    $module->order = $index + 1;
                    $module->course_id = $course->id; // Pastikan relasi terisi.
                }

                $course->courseModules()->saveMany($modules);

                // Untuk setiap module yang baru dibuat:
                $modules->each(function ($module) {
                    // Membuat 3 hingga 10 lesson (pelajaran) untuk module ini.
                    LessonDetail::factory(rand(3, 10))->create([
                        'module_id' => $module->id,
                    ]);
                });
            });
        });
    }
}