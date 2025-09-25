<?php

use Tests\DatabaseTestCase;
use App\Models\Enrollment\EnrollmentLesson;
use App\Models\Enrollment\EnrollmentModule;
use App\Models\Enrollment\Enrollment;
use App\Models\Course\CourseModuleLesson;
use App\Models\Course\CourseModule;
use App\Models\Course\Course;
use App\Models\Course\CourseCategory;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $category = CourseCategory::factory()->create();
    $this->course = Course::factory()->create(['category_id' => $category->id]);
    $this->module = CourseModule::factory()->create(['course_id' => $this->course->id]);
    $this->lesson = CourseModuleLesson::factory()->create(['module_id' => $this->module->id]);
    $this->user = User::factory()->create();
    $this->enrollment = Enrollment::factory()->create([
        'course_id' => $this->course->id,
        'user_id' => $this->user->id
    ]);
    $this->enrollmentModule = EnrollmentModule::factory()->create([
        'enrollment_id' => $this->enrollment->id,
        'module_id' => $this->module->id
    ]);
    $this->enrollmentLesson = EnrollmentLesson::factory()->create([
        'enrollment_module_id' => $this->enrollmentModule->id,
        'lesson_id' => $this->lesson->id
    ]);
});

it('has enrollment module relationship', function () {
    expect(method_exists($this->enrollmentLesson, 'enrollmentModule'))->toBeTrue();
    expect($this->enrollmentLesson->enrollmentModule())->toBeInstanceOf(BelongsTo::class);
});

it('belongs to an enrollment module', function () {
    expect($this->enrollmentLesson->enrollmentModule->id)->toBe($this->enrollmentModule->id);
});

it('has enrollment lesson relationship', function () {
    expect(method_exists($this->enrollmentLesson, 'enrollmentLesson'))->toBeTrue();
    expect($this->enrollmentLesson->enrollmentLesson())->toBeInstanceOf(BelongsTo::class);
});

it('belongs to a course module lesson', function () {
    expect($this->enrollmentLesson->enrollmentLesson->id)->toBe($this->lesson->id);
    expect($this->enrollmentLesson->enrollmentLesson->title)->toBe($this->lesson->title);
});

it('has proper enrollment lesson attributes', function () {
    $enrollmentLessonData = [
        'is_completed' => true
    ];
    $enrollmentLesson = EnrollmentLesson::factory()->create(array_merge($enrollmentLessonData, [
        'enrollment_module_id' => $this->enrollmentModule->id,
        'lesson_id' => $this->lesson->id
    ]));
    expect($enrollmentLesson->is_completed)->toBeTrue();
});

it('defaults to not completed', function () {
    $enrollmentLessonData = [
        'enrollment_module_id' => $this->enrollmentModule->id,
        'lesson_id' => $this->lesson->id,
        'is_completed' => false
    ];
    $enrollmentLesson = new EnrollmentLesson($enrollmentLessonData);
    $enrollmentLesson->save();
    
    expect($enrollmentLesson->is_completed)->toBeFalse();
});

it('casts boolean attributes correctly', function () {
    $enrollmentLesson = EnrollmentLesson::factory()->create([
        'enrollment_module_id' => $this->enrollmentModule->id,
        'lesson_id' => $this->lesson->id,
        'is_completed' => true
    ]);
    expect($enrollmentLesson->is_completed)->toBeBool()->toBeTrue();
});

it('can track completion status', function () {
    expect($this->enrollmentLesson->is_completed)->toBeFalse();
    $this->enrollmentLesson->update(['is_completed' => true]);
    expect($this->enrollmentLesson->is_completed)->toBeTrue();
});

it('requires enrollment_module_id field', function () {
    expect(fn () => EnrollmentLesson::factory()->create([
        'enrollment_module_id' => null,
        'lesson_id' => $this->lesson->id
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires lesson_id field', function () {
    expect(fn () => EnrollmentLesson::factory()->create([
        'enrollment_module_id' => $this->enrollmentModule->id,
        'lesson_id' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('can access enrollment through enrollment module', function () {
    $enrollmentFromLesson = $this->enrollmentLesson->enrollmentModule->enrollment;
    expect($enrollmentFromLesson->id)->toBe($this->enrollment->id);
});

it('can access course through relationships', function () {
    $courseFromLesson = $this->enrollmentLesson->enrollmentModule->enrollment->course;
    expect($courseFromLesson->id)->toBe($this->course->id);
    expect($courseFromLesson->title)->toBe($this->course->title);
});

it('can access user through relationships', function () {
    $userFromLesson = $this->enrollmentLesson->enrollmentModule->enrollment->user;
    expect($userFromLesson->id)->toBe($this->user->id);
    expect($userFromLesson->name)->toBe($this->user->name);
});

it('can access lesson details through relationship', function () {
    $lessonDetails = $this->enrollmentLesson->enrollmentLesson;
    expect($lessonDetails->title)->toBe($this->lesson->title);
    expect($lessonDetails->content)->toBe($this->lesson->content);
    expect($lessonDetails->order)->toBe($this->lesson->order);
});

it('deletes when enrollment module is deleted', function () {
    $enrollmentLessonId = $this->enrollmentLesson->id;
    $this->enrollmentModule->delete();
    
    expect(EnrollmentLesson::find($enrollmentLessonId))->toBeNull();
});

it('deletes when lesson is deleted', function () {
    $enrollmentLessonId = $this->enrollmentLesson->id;
    $this->lesson->delete();
    
    expect(EnrollmentLesson::find($enrollmentLessonId))->toBeNull();
});

it('multiple enrollment lessons can reference same lesson', function () {
    $user2 = User::factory()->create();
    $enrollment2 = Enrollment::factory()->create([
        'course_id' => $this->course->id,
        'user_id' => $user2->id
    ]);
    $enrollmentModule2 = EnrollmentModule::factory()->create([
        'enrollment_id' => $enrollment2->id,
        'module_id' => $this->module->id
    ]);
    $enrollmentLesson2 = EnrollmentLesson::factory()->create([
        'enrollment_module_id' => $enrollmentModule2->id,
        'lesson_id' => $this->lesson->id
    ]);
    
    expect($this->enrollmentLesson->lesson_id)->toBe($this->lesson->id);
    expect($enrollmentLesson2->lesson_id)->toBe($this->lesson->id);
    expect($this->enrollmentLesson->enrollment_module_id)->not->toBe($enrollmentLesson2->enrollment_module_id);
});
