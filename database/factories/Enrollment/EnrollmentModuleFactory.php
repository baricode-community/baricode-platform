<?php

namespace Database\Factories\Enrollment;

use App\Models\Enrollment\EnrollmentModule;
use App\Models\Enrollment\Enrollment;
use App\Models\Learning\CourseModule;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment\EnrollmentModule>
 */
class EnrollmentModuleFactory extends Factory
{
    protected $model = EnrollmentModule::class;

    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'module_id' => CourseModule::factory(),
            'is_completed' => false,
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
            'approval_notes' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'approved_by' => User::factory(),
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
