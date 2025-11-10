<?php

namespace Database\Factories\Enrollment;

use App\Models\Enrollment\EnrollmentLesson;
use App\Models\Enrollment\EnrollmentModule;
use App\Models\Learning\CourseModuleLesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment\EnrollmentLesson>
 */
class EnrollmentLessonFactory extends Factory
{
    protected $model = EnrollmentLesson::class;

    public function definition(): array
    {
        return [
            'enrollment_module_id' => EnrollmentModule::factory(),
            'lesson_id' => CourseModuleLesson::factory(),
            'is_completed' => false, // Default to false for testing
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
        ]);
    }
}
