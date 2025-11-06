<?php

namespace Database\Seeders;

use App\Models\ProyekBareng;
use App\Models\ProyekBarengKanboardLink;
use App\Models\ProyekBarengUsefulLink;
use App\Models\User\User;
use App\Models\Meet;
use App\Models\Kanboard;
use App\Models\Poll;
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

            // Create external kanboard links (1-3 per project)
            $externalKanboards = [
                [
                    'title' => 'Trello Board - ' . $proyekBareng->title,
                    'link' => 'https://trello.com/b/example/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Kanban board utama untuk tracking progress dan task management'
                ],
                [
                    'title' => 'Notion Workspace',
                    'link' => 'https://notion.so/workspace/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Dokumentasi proyek dan knowledge base lengkap'
                ],
                [
                    'title' => 'Asana Project',
                    'link' => 'https://app.asana.com/0/project/' . rand(1000000, 9999999),
                    'description' => 'Timeline dan milestone tracking untuk project management'
                ],
                [
                    'title' => 'Monday.com Board',
                    'link' => 'https://monday.com/boards/' . rand(100000, 999999),
                    'description' => 'Workflow automation dan team collaboration'
                ],
                [
                    'title' => 'Jira Board',
                    'link' => 'https://company.atlassian.net/jira/projects/' . strtoupper(substr($proyekBareng->id, 0, 3)),
                    'description' => 'Bug tracking dan agile project management'
                ]
            ];

            // Randomly select 1-3 external kanboards for each project
            $selectedExternalKanboards = collect($externalKanboards)->random(rand(1, 3));
            foreach ($selectedExternalKanboards as $externalKanboard) {
                ProyekBarengKanboardLink::create([
                    'proyek_bareng_id' => $proyekBareng->id,
                    'title' => $externalKanboard['title'],
                    'link' => $externalKanboard['link'],
                    'description' => $externalKanboard['description']
                ]);
            }

            // Attach random polls (1-3 polls per project)
            $polls = Poll::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($polls as $poll) {
                $pollTitles = [
                    'Voting Prioritas Fitur',
                    'Pemilihan Tech Stack',
                    'Jadwal Meeting Tim',
                    'Design Approval',
                    'Sprint Planning Vote',
                    'Final Decision Making',
                    'Budget Allocation',
                    'Resource Assignment'
                ];

                $pollDescriptions = [
                    'Voting untuk menentukan prioritas pengembangan fitur dalam proyek',
                    'Survey untuk memilih teknologi yang akan digunakan dalam pengembangan',
                    'Polling untuk menentukan waktu meeting yang cocok untuk semua anggota tim',
                    'Voting untuk approval design dan mockup yang telah dibuat',
                    'Survey untuk perencanaan sprint dan task assignment',
                    'Polling untuk pengambilan keputusan penting dalam proyek',
                    'Voting untuk alokasi budget dan resource management',
                    'Survey untuk pembagian tugas dan tanggung jawab anggota tim'
                ];

                $proyekBareng->polls()->attach($poll->id, [
                    'title' => collect($pollTitles)->random(),
                    'description' => collect($pollDescriptions)->random()
                ]);
            }

            // Create useful links for the project (2-4 links per project)
            $usefulLinks = [
                [
                    'title' => 'GitHub Repository',
                    'link' => 'https://github.com/company/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Repository utama untuk source code dan version control proyek'
                ],
                [
                    'title' => 'Figma Design File',
                    'link' => 'https://figma.com/file/' . rand(100000, 999999) . '/' . urlencode($proyekBareng->title),
                    'description' => 'Design system dan prototype UI/UX untuk proyek'
                ],
                [
                    'title' => 'API Documentation',
                    'link' => 'https://docs.api.company.com/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Dokumentasi lengkap API endpoints dan integration guide'
                ],
                [
                    'title' => 'Deployment Guide',
                    'link' => 'https://docs.deployment.company.com/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Panduan deployment dan configuration untuk production environment'
                ],
                [
                    'title' => 'Testing Guidelines',
                    'link' => 'https://testing.company.com/projects/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Guidelines untuk unit testing, integration testing, dan quality assurance'
                ],
                [
                    'title' => 'Style Guide',
                    'link' => 'https://styleguide.company.com/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Panduan coding standard dan best practices untuk consistency'
                ],
                [
                    'title' => 'Learning Resources',
                    'link' => 'https://learning.company.com/resources/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Kumpulan tutorial, artikel, dan referensi untuk skill development'
                ],
                [
                    'title' => 'Project Roadmap',
                    'link' => 'https://roadmap.company.com/projects/' . strtolower(str_replace(' ', '-', $proyekBareng->title)),
                    'description' => 'Timeline proyek, milestone, dan planning jangka panjang'
                ]
            ];

            // Randomly select 2-4 useful links for each project
            $selectedUsefulLinks = collect($usefulLinks)->random(rand(2, 4));
            foreach ($selectedUsefulLinks as $usefulLink) {
                ProyekBarengUsefulLink::create([
                    'proyek_bareng_id' => $proyekBareng->id,
                    'title' => $usefulLink['title'],
                    'link' => $usefulLink['link'],
                    'description' => $usefulLink['description']
                ]);
            }
        }
    }
}
