<?php

namespace Database\Factories\User;

use App\Models\User\UserNote;
use App\Models\User\User;
use App\Models\Course\CourseModuleLesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\UserNote>
 */
class UserNoteFactory extends Factory
{
    protected $model = UserNote::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => CourseModuleLesson::factory(),
            'title' => fake()->sentence(3),
            'note' => fake()->paragraph(3),
        ];
    }
}
