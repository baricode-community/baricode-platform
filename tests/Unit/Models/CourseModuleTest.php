<?php

use Tests\DatabaseTestCase;
use App\Models\Learning\CourseModule;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseModuleLesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $category = CourseCategory::factory()->create();
    $this->course = Course::factory()->create(['category_id' => $category->id]);
    $this->module = CourseModule::factory()->create(['course_id' => $this->course->id]);
});

it('has course relationship', function () {
    expect(method_exists($this->module, 'course'))->toBeTrue();
    expect($this->module->course())->toBeInstanceOf(BelongsTo::class);
});

it('belongs to a course', function () {
    expect($this->module->course->id)->toBe($this->course->id);
    expect($this->module->course->title)->toBe($this->course->title);
});

it('has course module lessons relationship', function () {
    expect(method_exists($this->module, 'courseModuleLessons'))->toBeTrue();
    expect($this->module->courseModuleLessons())->toBeInstanceOf(HasMany::class);
});

it('can have many course module lessons', function () {
    $lessons = collect();
    for ($i = 1; $i <= 4; $i++) {
        $lessons->push(CourseModuleLesson::factory()->create([
            'module_id' => $this->module->id,
            'order' => $i
        ]));
    }

    $retrievedLessons = $this->module->courseModuleLessons()->get();
    
    expect($retrievedLessons)->toHaveCount(4);
    
    // Test ordering
    expect($retrievedLessons->pluck('id')->toArray())
        ->toBe($lessons->sortBy('order')->pluck('id')->toArray());
});

it('orders lessons by order field', function () {
    // Create lessons with specific orders
    $lesson3 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 3]);
    $lesson1 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 1]);
    $lesson2 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 2]);

    $orderedLessons = $this->module->courseModuleLessons()->get();
    
    expect($orderedLessons[0]->id)->toBe($lesson1->id);
    expect($orderedLessons[1]->id)->toBe($lesson2->id);
    expect($orderedLessons[2]->id)->toBe($lesson3->id);
});

it('has proper module attributes', function () {
    $moduleData = [
        'name' => 'Introduction to PHP',
        'description' => 'Learn PHP basics and syntax',
        'order' => 1
    ];
    
    $module = CourseModule::factory()->create(array_merge($moduleData, ['course_id' => $this->course->id]));

    expect($module->name)->toBe($moduleData['name']);
    expect($module->description)->toBe($moduleData['description']);
    expect($module->order)->toBe($moduleData['order']);
});

it('has unique order per course', function () {
    $course1 = $this->course;
    $category2 = CourseCategory::factory()->create();
    $course2 = Course::factory()->create(['category_id' => $category2->id]);

    // Same order is allowed for different courses
    $module1 = CourseModule::factory()->create(['course_id' => $course1->id, 'order' => 1]);
    $module2 = CourseModule::factory()->create(['course_id' => $course2->id, 'order' => 1]);

    expect($module1->order)->toBe(1);
    expect($module2->order)->toBe(1);

    // But not for the same course
    expect(fn () => CourseModule::factory()->create(['course_id' => $course1->id, 'order' => 1]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('defaults order to negative one', function () {
    // Test with direct creation
    $moduleData = [
        'course_id' => $this->course->id,
        'name' => 'Test Module',
        'description' => 'Test Description'
    ];
    
    $module = new CourseModule($moduleData);
    $module->save();
    
    // Check database default using expect with database assertion
    $this->assertDatabaseHas('course_modules', [
        'name' => 'Test Module',
        'order' => -1
    ]);
});

it('can have null description', function () {
    $module = CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'description' => null
    ]);
    
    expect($module->description)->toBeNull();
});

it('deletes related data when module is deleted', function () {
    $lessons = CourseModuleLesson::factory()->count(3)->create([
        'module_id' => $this->module->id
    ]);

    $moduleId = $this->module->id;
    
    // Delete module
    $this->module->delete();

    // Verify cascading deletes
    foreach ($lessons as $lesson) {
        $this->assertDatabaseMissing('course_module_lessons', ['id' => $lesson->id]);
    }
});

it('requires name field', function () {
    expect(fn () => CourseModule::factory()->create([
        'course_id' => $this->course->id,
        'name' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires course_id field', function () {
    expect(fn () => CourseModule::factory()->create(['course_id' => null]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
