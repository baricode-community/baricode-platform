<?php

use Tests\DatabaseTestCase;
use App\Models\Enrollment\EnrollmentModule;
use App\Models\Enrollment\EnrollmentLesson;
use App\Models\Enrollment\Enrollment;
use App\Models\Course\CourseModule;
use App\Models\Course\Course;
use App\Models\Course\CourseCategory;
use App\Models\Course\CourseModuleLesson;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $category = CourseCategory::factory()->create();
    $this->course = Course::factory()->create(['category_id' => $category->id]);
    $this->module = CourseModule::factory()->create(['course_id' => $this->course->id]);
    $this->user = User::factory()->create();
    $this->enrollment = Enrollment::factory()->create([
        'course_id' => $this->course->id,
        'user_id' => $this->user->id
    ]);
    $this->enrollmentModule = EnrollmentModule::factory()->create([
        'enrollment_id' => $this->enrollment->id,
        'module_id' => $this->module->id
    ]);
});

it('has enrollment relationship', function () {
    expect(method_exists($this->enrollmentModule, 'enrollment'))->toBeTrue();
    expect($this->enrollmentModule->enrollment())->toBeInstanceOf(BelongsTo::class);
});

it('belongs to an enrollment', function () {
    expect($this->enrollmentModule->enrollment->id)->toBe($this->enrollment->id);
});

it('has enrollment lessons relationship', function () {
    expect(method_exists($this->enrollmentModule, 'enrollmentLessons'))->toBeTrue();
    expect($this->enrollmentModule->enrollmentLessons())->toBeInstanceOf(HasMany::class);
});

it('can have many enrollment lessons', function () {
    // Create lessons for the module
    $lessons = CourseModuleLesson::factory()->count(3)->create(['module_id' => $this->module->id]);
    
    // Create enrollment lessons
    $enrollmentLessons = collect();
    foreach ($lessons as $lesson) {
        $enrollmentLessons->push(EnrollmentLesson::factory()->create([
            'enrollment_module_id' => $this->enrollmentModule->id,
            'lesson_id' => $lesson->id
        ]));
    }

    $retrievedLessons = $this->enrollmentModule->enrollmentLessons()->get();
    
    expect($retrievedLessons)->toHaveCount(3);
    expect($retrievedLessons->pluck('id')->sort()->toArray())
        ->toBe($enrollmentLessons->pluck('id')->sort()->toArray());
});

it('has proper enrollment module attributes', function () {
    $approver = User::factory()->create();
    $enrollmentModuleData = [
        'is_completed' => true,
        'is_approved' => true,
        'approved_by' => $approver->id,
        'approved_at' => now(),
        'approval_notes' => 'Module completed successfully'
    ];
    
    $enrollmentModule = EnrollmentModule::factory()->create(array_merge($enrollmentModuleData, [
        'enrollment_id' => $this->enrollment->id,
        'module_id' => $this->module->id
    ]));

    expect($enrollmentModule->is_completed)->toBeTrue();
    expect($enrollmentModule->is_approved)->toBeTrue();
    expect($enrollmentModule->approved_by)->toBe($approver->id);
    expect($enrollmentModule->approved_at)->not->toBeNull();
    expect($enrollmentModule->approval_notes)->toBe($enrollmentModuleData['approval_notes']);
});

it('defaults to not completed and not approved', function () {
    // Test with direct creation
    $enrollmentModuleData = [
        'enrollment_id' => $this->enrollment->id,
        'module_id' => $this->module->id
    ];
    
    $enrollmentModule = new EnrollmentModule($enrollmentModuleData);
    $enrollmentModule->save();
    
    // Check database defaults
    $this->assertDatabaseHas('enrollment_modules', [
        'enrollment_id' => $this->enrollment->id,
        'module_id' => $this->module->id,
        'is_completed' => false,
        'is_approved' => false
    ]);
});

it('can have approval details', function () {
    $approver = User::factory()->create();
    $approvalTime = now();
    
    $this->enrollmentModule->update([
        'is_approved' => true,
        'approved_by' => $approver->id,
        'approved_at' => $approvalTime,
        'approval_notes' => 'Good progress on this module'
    ]);

    expect($this->enrollmentModule->is_approved)->toBeTrue();
    expect($this->enrollmentModule->approved_by)->toBe($approver->id);
    expect($this->enrollmentModule->approved_at->format('Y-m-d H:i:s'))
        ->toBe($approvalTime->format('Y-m-d H:i:s'));
    expect($this->enrollmentModule->approval_notes)->toBe('Good progress on this module');
});

it('can have null approval details', function () {
    $enrollmentModule = EnrollmentModule::factory()->create([
        'enrollment_id' => $this->enrollment->id,
        'module_id' => $this->module->id,
        'approved_by' => null,
        'approved_at' => null,
        'approval_notes' => null
    ]);

    expect($enrollmentModule->approved_by)->toBeNull();
    expect($enrollmentModule->approved_at)->toBeNull();
    expect($enrollmentModule->approval_notes)->toBeNull();
});

it('deletes related data when enrollment module is deleted', function () {
    // Create lessons and their corresponding enrollment lessons
    $lessons = CourseModuleLesson::factory()->count(2)->create(['module_id' => $this->module->id]);
    
    $enrollmentLessons = collect();
    foreach ($lessons as $lesson) {
        $enrollmentLessons->push(EnrollmentLesson::factory()->create([
            'enrollment_module_id' => $this->enrollmentModule->id,
            'lesson_id' => $lesson->id
        ]));
    }

    $enrollmentModuleId = $this->enrollmentModule->id;
    
    // Delete enrollment module
    $this->enrollmentModule->delete();

    // Verify cascading deletes
    foreach ($enrollmentLessons as $enrollmentLesson) {
        $this->assertDatabaseMissing('enrollment_lessons', ['id' => $enrollmentLesson->id]);
    }
});

it('requires enrollment_id field', function () {
    expect(fn () => EnrollmentModule::factory()->create([
        'enrollment_id' => null,
        'module_id' => $this->module->id
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires module_id field', function () {
    expect(fn () => EnrollmentModule::factory()->create([
        'enrollment_id' => $this->enrollment->id,
        'module_id' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('sets approved_by to null when approver is deleted', function () {
    $approver = User::factory()->create();
    
    $this->enrollmentModule->update([
        'is_approved' => true,
        'approved_by' => $approver->id
    ]);
    
    // Delete the approver user
    $approver->delete();
    
    // Refresh enrollment module
    $this->enrollmentModule->refresh();
    
    // approved_by should be set to null due to foreign key constraint
    expect($this->enrollmentModule->approved_by)->toBeNull();
});

it('can access course through enrollment', function () {
    // Test accessing course through the enrollment relationship
    $courseFromEnrollmentModule = $this->enrollmentModule->enrollment->course;
    
    expect($courseFromEnrollmentModule->id)->toBe($this->course->id);
    expect($courseFromEnrollmentModule->title)->toBe($this->course->title);
});

it('can access user through enrollment', function () {
    // Test accessing user through the enrollment relationship
    $userFromEnrollmentModule = $this->enrollmentModule->enrollment->user;
    
    expect($userFromEnrollmentModule->id)->toBe($this->user->id);
    expect($userFromEnrollmentModule->name)->toBe($this->user->name);
});

it('can track completion status', function () {
    // Initially not completed
    expect($this->enrollmentModule->is_completed)->toBeFalse();
    
    // Mark as completed
    $this->enrollmentModule->update(['is_completed' => true]);
    
    expect($this->enrollmentModule->is_completed)->toBeTrue();
});
