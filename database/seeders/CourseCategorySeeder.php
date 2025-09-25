<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course\CourseCategory;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Untuk pemula yang baru belajar ngoding
        CourseCategory::create(['name' => 'Cara Kerja Komputer']);
        CourseCategory::create(['name' => 'Dasar Pemrograman']);
        CourseCategory::create(['name' => 'Algoritma & Logika']);

        // Untuk tingkat menengah
        CourseCategory::create(['name' => 'Pemrograman Web Dasar']);
        CourseCategory::create(['name' => 'Pemrograman Mobile Dasar']);
        CourseCategory::create(['name' => 'Pemrograman Berorientasi Objek']);
        CourseCategory::create(['name' => 'Database & Penyimpanan']);
        CourseCategory::create(['name' => 'Framework']);
        CourseCategory::create(['name' => 'Version Control (Git)']);

        // Untuk tingkat mahir
        CourseCategory::create(['name' => 'Pengembangan API']);
        CourseCategory::create(['name' => 'DevOps & Deployment']);
        CourseCategory::create(['name' => 'Testing & Debugging']);

        // Saran tambahan kategori
        CourseCategory::create(['name' => 'Keamanan Aplikasi']);
        CourseCategory::create(['name' => 'Cloud Computing']);
        CourseCategory::create(['name' => 'UI/UX Design']);
        CourseCategory::create(['name' => 'Machine Learning Dasar']);
        CourseCategory::create(['name' => 'Data Science']);
        CourseCategory::create(['name' => 'Open Source Contribution']);
    }
}
