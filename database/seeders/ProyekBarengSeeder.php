<?php

namespace Database\Seeders;

use App\Models\ProyekBareng;
use App\Models\User\User;
use App\Models\Meet;
use App\Models\Kanboard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProyekBarengSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample ProyekBareng records
        $proyekBarengData = [
            [
                'title' => 'Platform Edukasi Online',
                'description' => 'Proyek kolaboratif untuk mengembangkan platform pembelajaran online yang inovatif dengan fitur interaktif dan gamifikasi.',
                'is_finished' => false
            ],
            [
                'title' => 'Aplikasi Manajemen Task',
                'description' => 'Membangun aplikasi manajemen task modern dengan fitur kanban board, tracking waktu, dan kolaborasi tim.',
                'is_finished' => true
            ],
            [
                'title' => 'Website Portfolio Kreatif',
                'description' => 'Mengembangkan website portfolio dengan desain yang menarik dan fitur showcase project yang interaktif.',
                'is_finished' => false
            ],
            [
                'title' => 'Mobile App E-Commerce',
                'description' => 'Proyek pengembangan aplikasi mobile e-commerce dengan fitur lengkap mulai dari katalog hingga payment gateway.',
                'is_finished' => true
            ],
            [
                'title' => 'Dashboard Analytics',
                'description' => 'Membangun dashboard analytics real-time untuk monitoring performa bisnis dengan visualisasi data yang menarik.',
                'is_finished' => false
            ]
        ];

        foreach ($proyekBarengData as $data) {
            $proyekBareng = ProyekBareng::create($data);

            // Attach random users (3-5 users per project)
            $users = User::inRandomOrder()->limit(rand(3, 5))->get();
            foreach ($users as $user) {
                $proyekBareng->users()->attach($user->id, [
                    'description' => 'Berpartisipasi sebagai ' . collect(['Developer', 'Designer', 'Project Manager', 'QA Tester', 'DevOps'])->random()
                ]);
            }

            // Attach random meets (1-3 meets per project)
            $meets = Meet::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($meets as $meet) {
                $proyekBareng->meets()->attach($meet->id, [
                    'description' => 'Meeting untuk ' . collect(['Planning', 'Review Progress', 'Demo', 'Standup', 'Retrospective'])->random()
                ]);
            }

            // Attach random kanboards (1-2 kanboards per project)
            $kanboards = Kanboard::inRandomOrder()->limit(rand(1, 2))->get();
            foreach ($kanboards as $kanboard) {
                $proyekBareng->kanboards()->attach($kanboard->id, [
                    'description' => 'Kanboard untuk ' . collect(['Development Tasks', 'Design Tasks', 'Bug Tracking', 'Feature Planning'])->random()
                ]);
            }
        }
    }
}
