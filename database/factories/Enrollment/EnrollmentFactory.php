<?php

namespace Database\Factories;

use App\Models\CourseEnrollment;
use App\Models\Enrollment\Enrollment;
use App\Models\User\User;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Enrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'is_approved' => $this->faker->boolean(),
        ];
    }

    /**
     * Indicate that the enrollment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'approval_notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the enrollment is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
            'approval_notes' => null,
        ]);
    }
}
