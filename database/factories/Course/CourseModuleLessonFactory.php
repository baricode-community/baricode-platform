<?php

namespace Database\Factories\Course;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseModuleLessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'module_id' => \App\Models\Course\CourseModule::factory(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}