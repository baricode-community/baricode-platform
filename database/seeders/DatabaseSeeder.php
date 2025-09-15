<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Course;
use App\Models\Module;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::beginTransaction();

        User::factory(10)->create();
        Course::factory(20)->create()->each(function ($course) {
            $modules = Module::factory(rand(4, 15))->make();
            foreach ($modules as $index => $module) {
                $module->order = $index + 1;
            }
            $course->modules()->saveMany($modules);
        });

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        DB::commit();
    }
}
