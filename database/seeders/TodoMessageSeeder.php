<?php

namespace Database\Seeders;

use App\Models\KanboardTodo;
use App\Models\TodoMessage;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a few todos to seed with messages
        $todos = KanboardTodo::with(['assignedUsers', 'card.kanboard'])->take(5)->get();
        $users = User::take(3)->get();

        if ($todos->count() === 0 || $users->count() === 0) {
            $this->command->info('No todos or users found. Create some todos and users first.');
            return;
        }

        foreach ($todos as $todo) {
            // Create 2-5 messages per todo
            $messageCount = rand(2, 5);
            
            for ($i = 0; $i < $messageCount; $i++) {
                $randomUser = $users->random();
                
                // Create sample messages
                $messages = [
                    'Halo, saya mulai mengerjakan task ini.',
                    'Ada kendala di bagian ini, butuh bantuan.',
                    'Sudah progress 50%, estimasi selesai besok.',
                    'Task ini sudah selesai, mohon direview.',
                    'Terima kasih atas feedbacknya.',
                    'Ada revisi yang perlu dilakukan?',
                    'Sudah diperbaiki sesuai saran.',
                    'Butuh informasi tambahan untuk melanjutkan.',
                ];
                
                TodoMessage::create([
                    'kanboard_todo_id' => $todo->id,
                    'user_id' => $randomUser->id,
                    'message' => $messages[array_rand($messages)],
                    'created_at' => now()->subMinutes(rand(1, 1440)), // Random time in last 24 hours
                ]);
            }
        }

        $this->command->info('Todo messages seeded successfully.');
    }
}
