<?php

use Tests\DatabaseTestCase;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Course;
use App\Models\Learning\CourseModule;
use App\Models\Learning\CourseModuleLesson;
use Livewire\Volt\Volt;

uses(DatabaseTestCase::class);

it('can render lesson management page', function () {
    Volt::test('admin.lesson-management')
        ->assertSuccessful()
        ->assertSee('Manajemen Pelajaran')
        ->assertSee('Tambah Pelajaran');
});

it('can create new lesson', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    
    Volt::test('admin.lesson-management')
        ->call('openCreateModal')
        ->set('title', 'Test Lesson')
        ->set('content', 'Test markdown content')
        ->set('module_id', $module->id)
        ->set('order', 1)
        ->call('save')
        ->assertSet('showModal', false);
    
    expect(CourseModuleLesson::where('title', 'Test Lesson')->first())
        ->not->toBeNull()
        ->content->toBe('Test markdown content')
        ->module_id->toBe($module->id)
        ->order->toBe(1);
});

it('can update existing lesson', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    $lesson = CourseModuleLesson::factory()->create(['module_id' => $module->id]);
    
    Volt::test('admin.lesson-management')
        ->call('openEditModal', $lesson->id)
        ->set('title', 'Updated Lesson')
        ->set('content', 'Updated markdown content')
        ->call('save')
        ->assertSet('showModal', false);
    
    $updated = CourseModuleLesson::find($lesson->id);
    expect($updated)
        ->not->toBeNull()
        ->title->toBe('Updated Lesson')
        ->content->toBe('Updated markdown content');
});

it('validates required fields when creating lesson', function () {
    Volt::test('admin.lesson-management')
        ->call('openCreateModal')
        ->set('title', '')
        ->set('module_id', '')
        ->call('save')
        ->assertHasErrors(['title', 'module_id'])
        ->assertSet('showModal', true);
});

it('can delete lesson', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    $lesson = CourseModuleLesson::factory()->create(['module_id' => $module->id]);
    
    Volt::test('admin.lesson-management')
        ->call('delete', $lesson->id);
    
    expect(CourseModuleLesson::find($lesson->id))->toBeNull();
});

it('can move lesson up in order', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    
    $lesson1 = CourseModuleLesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
    $lesson2 = CourseModuleLesson::factory()->create(['module_id' => $module->id, 'order' => 2]);
    
    Volt::test('admin.lesson-management')
        ->call('moveUp', $lesson2->id);
    
    expect(CourseModuleLesson::find($lesson1->id)->order)->toBe(2);
    expect(CourseModuleLesson::find($lesson2->id)->order)->toBe(1);
});

it('can move lesson down in order', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    
    $lesson1 = CourseModuleLesson::factory()->create(['module_id' => $module->id, 'order' => 1]);
    $lesson2 = CourseModuleLesson::factory()->create(['module_id' => $module->id, 'order' => 2]);
    
    Volt::test('admin.lesson-management')
        ->call('moveDown', $lesson1->id);
    
    expect(CourseModuleLesson::find($lesson1->id)->order)->toBe(2);
    expect(CourseModuleLesson::find($lesson2->id)->order)->toBe(1);
});

it('can search lessons', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    
    CourseModuleLesson::factory()->create(['title' => 'PHP Basics', 'module_id' => $module->id]);
    CourseModuleLesson::factory()->create(['title' => 'JavaScript Basics', 'module_id' => $module->id]);
    
    Volt::test('admin.lesson-management')
        ->set('search', 'PHP')
        ->assertSee('PHP Basics')
        ->assertDontSee('JavaScript Basics');
});

it('handles empty state correctly', function () {
    CourseModuleLesson::query()->delete();
    
    Volt::test('admin.lesson-management')
        ->assertSee('Belum ada pelajaran');
});
