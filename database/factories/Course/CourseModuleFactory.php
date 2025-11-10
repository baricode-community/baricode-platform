<?php

namespace Database\Factories\Course;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Learning\CourseModule>
 */
class CourseModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => \App\Models\Learning\Course::factory(),
            'name' => fake()->unique()->words(rand(2, 4), true),
            'order' => $this->faker->unique()->numberBetween(1, 1000)
        ];
    }
}