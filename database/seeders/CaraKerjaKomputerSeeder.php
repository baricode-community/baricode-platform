<?php

namespace Database\Seeders;

use App\Models\Course\Course;
use Illuminate\Database\Seeder;

class CaraKerjaKomputerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caraKerjaKomputer = Course::create([
            'title' => 'Pengantar Cara Kerja Komputer',
            'slug' => 'introduction-to-how-computers-work',
            'description' => 'Pelajari dasar-dasar cara kerja komputer, termasuk perangkat keras dan perangkat lunak yang membentuk sistem komputer modern.',
            'is_published' => true,
            'category_id' => 1,
        ]);

        $modules = [
            [
                'name' => 'Sejarah Komputer',
                'description' => 'Pelajari evolusi komputer dari masa ke masa, mulai dari komputer mekanik hingga komputer modern.',
                'order' => 1,
                'courseModuleLessons' => [
                    ['title' => 'Komputer Generasi Pertama', 'content' => 'Penjelasan tentang komputer generasi pertama dan karakteristiknya.'],
                    ['title' => 'Komputer Generasi Kedua', 'content' => 'Penjelasan tentang komputer generasi kedua dan perkembangannya.'],
                ]
            ],
            [
                'name' => 'Komponen Perangkat Keras',
                'description' => 'Kenali berbagai komponen perangkat keras komputer seperti CPU, RAM, hard drive, dan lainnya.',
                'order' => 2,
                'courseModuleLessons' => [
                    ['title' => 'CPU', 'content' => 'Fungsi dan cara kerja CPU.'],
                    ['title' => 'RAM', 'content' => 'Fungsi dan jenis-jenis RAM.'],
                    ['title' => 'Hard Drive', 'content' => 'Jenis dan fungsi hard drive.'],
                ]
            ],
            [
                'name' => 'Sistem Operasi',
                'description' => 'Pahami peran sistem operasi dalam mengelola perangkat keras dan perangkat lunak komputer.',
                'order' => 3,
                'courseModuleLessons' => [
                    ['title' => 'Pengertian Sistem Operasi', 'content' => 'Definisi dan fungsi sistem operasi.'],
                    ['title' => 'Jenis Sistem Operasi', 'content' => 'Macam-macam sistem operasi yang umum digunakan.'],
                ]
            ],
            [
                'name' => 'Perangkat Lunak Dasar',
                'description' => 'Pelajari tentang perangkat lunak dasar seperti BIOS, driver, dan utilitas sistem.',
                'order' => 4,
                'courseModuleLessons' => [
                    ['title' => 'BIOS', 'content' => 'Fungsi BIOS dalam komputer.'],
                    ['title' => 'Driver', 'content' => 'Peran driver dalam sistem komputer.'],
                ]
            ],
            [
                'name' => 'Jaringan Komputer',
                'description' => 'Dapatkan pemahaman dasar tentang jaringan komputer dan bagaimana komputer berkomunikasi satu sama lain.',
                'order' => 5,
                'courseModuleLessons' => [
                    ['title' => 'Pengertian Jaringan Komputer', 'content' => 'Definisi dan manfaat jaringan komputer.'],
                    ['title' => 'Jenis Jaringan', 'content' => 'LAN, WAN, dan jenis jaringan lainnya.'],
                ]
            ],
        ];

        foreach ($modules as $moduleData) {
            $lessons = $moduleData['courseModuleLessons'];
            unset($moduleData['courseModuleLessons']);
            $module = $caraKerjaKomputer->courseModules()->create($moduleData);
            $module->courseModuleLessons()->createMany($lessons);
        }
    }
}
