<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Module;

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
        Category::factory(50)->create()->each(function ($category) {
            // Membuat 3 hingga 7 course (kursus) untuk kategori ini.
            Course::factory(rand(3, 7))->create([
                'category_id' => $category->id,
            ])->each(function ($course) {
                // Membuat 4 hingga 15 module (modul) untuk setiap course.
                $modules = Module::factory(rand(4, 15))->make();

                // Menyimpan modules ke dalam course dan mengurutkannya.
                foreach ($modules as $index => $module) {
                    $module->order = $index + 1;
                    $module->course_id = $course->id; // Pastikan relasi terisi.
                }

                $course->modules()->saveMany($modules);
                
                // Untuk setiap module yang baru dibuat:
                $modules->each(function ($module) {
                    // Membuat 3 hingga 10 lesson (pelajaran) untuk module ini.
                    \App\Models\Lesson::factory(rand(3, 10))->create([
                        'module_id' => $module->id,
                    ]);
                });
            });
        });

        // Membuat pengguna 'test@example.com' jika belum ada.
        if (!User::where('email', 'test@example.com')->exists()) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $user->assignRole('admin');
        }
    }
}