<?php

namespace Database\Seeders;

use App\Models\Learning\Course;
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
            'slug' => 'pengantar-cara-kerja-komputer',
            'description' => 'Pelajari dasar-dasar cara kerja komputer, termasuk perangkat keras dan perangkat lunak yang membentuk sistem komputer modern. Kursus ini akan membimbing Anda melalui evolusi komputer, komponen utamanya, hingga cara mereka berinteraksi.',
            'is_published' => true,
            'category_id' => 1,
        ]);

        $modules = [
            [
                'name' => 'Sejarah dan Evolusi Komputer',
                'description' => 'Telusuri perjalanan menakjubkan komputer dari mesin hitung kuno hingga perangkat digital canggih yang kita gunakan hari ini.',
                'order' => 1,
                'courseModuleLessons' => [
                    [
                        'title' => 'Komputer Generasi Pertama: Tabung Vakum', 
                        'content' => 'Materi ini membahas era **komputer generasi pertama** (sekitar 1940-an hingga 1950-an) yang dicirikan oleh penggunaan **tabung vakum** yang besar dan menghasilkan panas. Contoh paling terkenal adalah **ENIAC**. Pelajari bagaimana keterbatasan teknologi ini membentuk dasar-dasar arsitektur komputer modern.'
                    ],
                    [
                        'title' => 'Komputer Generasi Kedua: Transistor', 
                        'content' => 'Pelajari bagaimana penemuan **transistor** pada tahun 1947 merevolusi dunia komputer. Transistor jauh lebih kecil, lebih cepat, dan lebih efisien dibandingkan tabung vakum. Era ini (sekitar 1950-an hingga 1960-an) juga memperkenalkan bahasa pemrograman tingkat tinggi.'
                    ],
                    [
                        'title' => 'Komputer Generasi Ketiga: IC dan Mikroprosesor', 
                        'content' => 'Pelajari bagaimana **sirkuit terpadu (IC)** memungkinkan ribuan transistor ditempatkan dalam satu chip silikon. Ini membuka jalan bagi **mikroprosesor** yang membawa komputer ke ukuran yang jauh lebih kecil dan terjangkau, memicu revolusi komputer pribadi.'
                    ],
                ]
            ],
            [
                'name' => 'Komponen Perangkat Keras (Hardware) Kunci',
                'description' => 'Kenali setiap bagian vital dari komputer, dari otak yang memproses data hingga penyimpanan yang menyimpan kenangan digital Anda.',
                'order' => 2,
                'courseModuleLessons' => [
                    [
                        'title' => 'Central Processing Unit (CPU)', 
                        'content' => '**CPU** adalah otak komputer. Materi ini menjelaskan bagaimana CPU mengambil instruksi, memproses data, dan menjalankan program. Kita akan membahas arsitektur seperti **ALU (Arithmetic Logic Unit)** dan **Control Unit** yang memungkinkan komputasi.'
                    ],
                    [
                        'title' => 'Random Access Memory (RAM)', 
                        'content' => '**RAM** adalah memori jangka pendek komputer. Pahami mengapa RAM sangat penting untuk kecepatan sistem, bagaimana ia menyimpan data sementara, dan perbedaan antara jenis-jenis RAM seperti **DDR4** dan **DDR5**.'
                    ],
                    [
                        'title' => 'Penyimpanan (Storage)', 
                        'content' => 'Pelajari tentang **penyimpanan jangka panjang** komputer. Kita akan membedah perbedaan antara **Hard Disk Drive (HDD)** yang menggunakan piringan magnetik dan **Solid State Drive (SSD)** yang berbasis flash memory, serta kelebihan dan kekurangannya.'
                    ],
                ]
            ],
            [
                'name' => 'Sistem Operasi: Jembatan Antara Hardware dan User',
                'description' => 'Pahami peran sistem operasi sebagai manajer utama komputer, yang mengatur sumber daya dan memungkinkan Anda berinteraksi dengan perangkat.',
                'order' => 3,
                'courseModuleLessons' => [
                    [
                        'title' => 'Pengertian dan Fungsi Sistem Operasi', 
                        'content' => 'Definisi **sistem operasi (OS)** sebagai perangkat lunak inti yang mengelola semua sumber daya perangkat keras dan perangkat lunak. Kita akan membahas fungsi utamanya, seperti manajemen memori, proses, dan file.'
                    ],
                    [
                        'title' => 'Jenis-Jenis Sistem Operasi', 
                        'content' => 'Materi ini memperkenalkan berbagai jenis sistem operasi yang umum digunakan, seperti **Windows**, **macOS**, dan **Linux**, serta kelebihan dan skenario penggunaannya masing-masing.'
                    ],
                    [
                        'title' => 'Proses Booting Komputer', 
                        'content' => 'Langkah demi langkah, kita akan mempelajari apa yang terjadi dari saat Anda menekan tombol power hingga sistem operasi muncul di layar. Pahami peran **BIOS/UEFI** dan urutan bootloader.'
                    ],
                ]
            ],
            [
                'name' => 'Perangkat Lunak Dasar dan Utilitas',
                'description' => 'Pelajari perangkat lunak penting yang bekerja di balik layar untuk menjaga sistem Anda tetap berjalan lancar.',
                'order' => 4,
                'courseModuleLessons' => [
                    [
                        'title' => 'BIOS dan UEFI', 
                        'content' => '**BIOS (Basic Input/Output System)** dan penerusnya, **UEFI (Unified Extensible Firmware Interface)**, adalah perangkat lunak pertama yang berjalan saat komputer dinyalakan. Pahami bagaimana mereka menginisialisasi hardware dan memulai sistem operasi.'
                    ],
                    [
                        'title' => 'Driver Perangkat', 
                        'content' => '**Driver** adalah perangkat lunak kecil yang memungkinkan sistem operasi berkomunikasi dengan perangkat keras tertentu, seperti kartu grafis atau printer. Pelajari mengapa driver penting dan bagaimana cara kerjanya.'
                    ],
                ]
            ],
            [
                'name' => 'Jaringan Komputer dan Internet',
                'description' => 'Dapatkan pemahaman dasar tentang bagaimana komputer berkomunikasi satu sama lain, dari jaringan lokal hingga jaringan global seperti internet.',
                'order' => 5,
                'courseModuleLessons' => [
                    [
                        'title' => 'Konsep Dasar Jaringan Komputer', 
                        'content' => 'Materi ini membahas apa itu **jaringan komputer**, mengapa kita membutuhkannya, dan model dasarnya, termasuk konsep **klien-server** dan **peer-to-peer**.'
                    ],
                    [
                        'title' => 'LAN, WAN, dan Internet', 
                        'content' => 'Pelajari perbedaan antara **Jaringan Area Lokal (LAN)** yang menghubungkan perangkat di area kecil dan **Jaringan Area Luas (WAN)** yang menjangkau jarak geografis yang lebih besar. Kita juga akan meninjau bagaimana **Internet** bekerja sebagai jaringan dari semua jaringan.'
                    ],
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