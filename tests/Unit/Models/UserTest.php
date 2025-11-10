<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use App\Models\User\UserNote;
use App\Models\Enrollment\Enrollment;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseModule;
use App\Models\Learning\CourseModuleLesson;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('has course enrollments relationship', function () {
    expect($this->user)->toBeInstanceOf(User::class);
    expect($this->user->courseEnrollments())->toBeInstanceOf(HasMany::class);
});

it('can have many course enrollments', function () {
    // Create courses for enrollment
    $category = CourseCategory::factory()->create();
    $courses = Course::factory()->count(3)->create(['category_id' => $category->id]);
    
    // Create enrollments
    $enrollments = collect();
    foreach ($courses as $course) {
        $enrollments->push(Enrollment::factory()->create([
            'user_id' => $this->user->id,
            'course_id' => $course->id
        ]));
    }

    expect($this->user->courseEnrollments()->get())->toHaveCount(3);
    expect($this->user->courseEnrollments()->get()->pluck('id')->sort()->values())
        ->toEqual($enrollments->pluck('id')->sort()->values());
});

it('has user notes relationship', function () {
    expect($this->user->userNotes())->toBeInstanceOf(HasMany::class);
});

it('can have many user notes', function () {
    // Create course structure for notes
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    $lessons = CourseModuleLesson::factory()->count(3)->create(['module_id' => $module->id]);

    // Create notes for different lessons
    $notes = collect();
    foreach ($lessons as $lesson) {
        $notes->push(UserNote::factory()->create([
            'user_id' => $this->user->id,
            'lesson_id' => $lesson->id
        ]));
    }

    expect($this->user->userNotes()->get())->toHaveCount(3);
    expect($this->user->userNotes()->get()->pluck('id')->sort()->values())
        ->toEqual($notes->pluck('id')->sort()->values());
});

it('can have roles and permissions', function () {
    // Test Spatie Permission integration
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => 'manage-users']);
    
    $role->givePermissionTo($permission);
    $this->user->assignRole($role);

    expect($this->user->hasRole('admin'))->toBeTrue();
    expect($this->user->hasPermissionTo('manage-users'))->toBeTrue();
    expect($this->user->roles)->toHaveCount(1);
});

it('has proper user attributes', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'whatsapp' => '+6281234567890',
        'level' => 'pemula'
    ];
    
    $user = User::factory()->create($userData);

    expect($user->name)->toBe($userData['name']);
    expect($user->email)->toBe($userData['email']);
    expect($user->whatsapp)->toBe($userData['whatsapp']);
    expect($user->level)->toBe($userData['level']);
});

it('generates correct initials', function () {
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Mary Smith']);
    
    expect($user1->initials())->toBe('JD');
    expect($user2->initials())->toBe('JM');
});

it('deletes related data when user is deleted', function () {
    // Create related data
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    $lesson = CourseModuleLesson::factory()->create(['module_id' => $module->id]);
    
    $enrollment = Enrollment::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course->id
    ]);
    
    $note = UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $lesson->id
    ]);

    $userId = $this->user->id;
    
    // Delete user
    $this->user->delete();

    // Verify cascading deletes
    $this->assertDatabaseMissing('enrollments', ['user_id' => $userId]);
    $this->assertDatabaseMissing('user_notes', ['user_id' => $userId]);
});