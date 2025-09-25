<?php

namespace Database\Seeders;

use App\Models\Course\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caraKerjaKomputer = Course::create(['title' => 'Pengantar Cara Kerja Komputer', 'slug' => 'introduction-to-how-computers-work', 'description' => 'Pelajari dasar-dasar cara kerja komputer, termasuk perangkat keras dan perangkat lunak yang membentuk sistem komputer modern.', 'is_published' => true, 'category_id' => 1]);
        $caraKerjaKomputer->courseModules()->createMany([
            ['name' => 'Sejarah Komputer', 'description' => 'Pelajari evolusi komputer dari masa ke masa, mulai dari komputer mekanik hingga komputer modern.', 'course_id' => $caraKerjaKomputer->id,'order' => 1],
            ['name' => 'Komponen Perangkat Keras', 'description' => 'Kenali berbagai komponen perangkat keras komputer seperti CPU, RAM, hard drive, dan lainnya.', 'course_id' => $caraKerjaKomputer->id,'order' => 2],
            ['name' => 'Sistem Operasi', 'description' => 'Pahami peran sistem operasi dalam mengelola perangkat keras dan perangkat lunak komputer.', 'course_id' => $caraKerjaKomputer->id,'order' => 3],
            ['name' => 'Perangkat Lunak Dasar', 'description' => 'Pelajari tentang perangkat lunak dasar seperti BIOS, driver, dan utilitas sistem.', 'course_id' => $caraKerjaKomputer->id,'order' => 4],
            ['name' => 'Jaringan Komputer', 'description' => 'Dapatkan pemahaman dasar tentang jaringan komputer dan bagaimana komputer berkomunikasi satu sama lain.', 'course_id' => $caraKerjaKomputer->id,'order' => 5],
            ['name' => 'Komponen Perangkat Keras', 'description' => 'Kenali berbagai komponen perangkat keras komputer seperti CPU, RAM, hard drive, dan lainnya.', 'course_id' => $caraKerjaKomputer->id,'order' => 6],
            ['name' => 'Sistem Operasi', 'description' => 'Pahami peran sistem operasi dalam mengelola perangkat keras dan perangkat lunak komputer.', 'course_id' => $caraKerjaKomputer->id,'order' => 7],
            ['name' => 'Perangkat Lunak Dasar', 'description' => 'Pelajari tentang perangkat lunak dasar seperti BIOS, driver, dan utilitas sistem.', 'course_id' => $caraKerjaKomputer->id,'order' => 8],
            ['name' => 'Jaringan Komputer', 'description' => 'Dapatkan pemahaman dasar tentang jaringan komputer dan bagaimana komputer berkomunikasi satu sama lain.', 'course_id' => $caraKerjaKomputer->id,'order' => 9],
        ]);
    }
}
