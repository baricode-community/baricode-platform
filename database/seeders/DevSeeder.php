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
        User::factory(10)->create();

        Category::factory(5)->create()->each(function ($category) {
            $courses = Course::factory(rand(3, 7))->make();
            $category->courses()->saveMany($courses);

            $courses->each(function ($course) {
                $modules = Module::factory(rand(4, 15))->make();
                foreach ($modules as $index => $module) {
                    $module->order = $index + 1;
                }
                $course->modules()->saveMany($modules);
            });
        });

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}