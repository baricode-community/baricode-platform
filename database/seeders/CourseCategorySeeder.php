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
        CourseCategory::create(['name' => 'Cara Kerja Komputer', 'level' => 'pemula']);
        CourseCategory::create(['name' => 'Dasar Pemrograman', 'level' => 'pemula']);
        CourseCategory::create(['name' => 'Algoritma & Logika', 'level' => 'pemula']);

        // Untuk tingkat menengah
        CourseCategory::create(['name' => 'Pemrograman Web Dasar', 'level' => 'menengah']);
        CourseCategory::create(['name' => 'Pemrograman Mobile Dasar', 'level' => 'menengah']);
        CourseCategory::create(['name' => 'Pemrograman Berorientasi Objek', 'level' => 'menengah']);
        CourseCategory::create(['name' => 'Database & Penyimpanan', 'level' => 'menengah']);
        CourseCategory::create(['name' => 'Framework', 'level' => 'menengah']);
        CourseCategory::create(['name' => 'UI/UX Design', 'level' => 'menengah']);
        CourseCategory::create(['name' => 'Version Control (Git)', 'level' => 'menengah']);

        // Untuk tingkat mahir
        CourseCategory::create(['name' => 'Pengembangan API', 'level' => 'lanjut']);
        CourseCategory::create(['name' => 'DevOps & Deployment', 'level' => 'lanjut']);
        CourseCategory::create(['name' => 'Testing & Debugging', 'level' => 'lanjut']);

        // Saran tambahan kategori
        CourseCategory::create(['name' => 'Keamanan Aplikasi', 'level' => 'lanjut']);
        CourseCategory::create(['name' => 'Cloud Computing', 'level' => 'lanjut']);
        CourseCategory::create(['name' => 'Machine Learning Dasar', 'level' => 'lanjut']);
        CourseCategory::create(['name' => 'Data Science', 'level' => 'lanjut']);
        CourseCategory::create(['name' => 'Open Source Contribution', 'level' => 'lanjut']);
    }
}
