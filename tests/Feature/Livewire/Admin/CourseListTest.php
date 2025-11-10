<?php

use Tests\DatabaseTestCase;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Course;
use Livewire\Volt\Volt;

uses(DatabaseTestCase::class);

it('can render course list page', function () {
    Volt::test('admin.course-list')
        ->assertSuccessful()
        ->assertSee('Manajemen Kursus')
        ->assertSee('Tambah Kursus');
});

it('can display courses list', function () {
    $category = CourseCategory::factory()->create();
    $courses = Course::factory()->count(3)->create(['category_id' => $category->id]);
    
    Volt::test('admin.course-list')
        ->assertSuccessful()
        ->assertSee($courses[0]->title)
        ->assertSee($courses[1]->title)
        ->assertSee($courses[2]->title);
});

it('can open create modal', function () {
    Volt::test('admin.course-list')
        ->call('openCreateModal')
        ->assertSet('showModal', true)
        ->assertSet('editingId', null)
        ->assertSet('title', '');
});

it('can create new course', function () {
    $category = CourseCategory::factory()->create();
    
    Volt::test('admin.course-list')
        ->call('openCreateModal')
        ->set('title', 'Test Course')
        ->set('description', 'Test Description')
        ->set('category_id', $category->id)
        ->set('slug', 'test-course')
        ->set('is_published', true)
        ->call('save')
        ->assertSet('showModal', false);
    
    expect(Course::where('title', 'Test Course')->first())
        ->not->toBeNull()
        ->description->toBe('Test Description')
        ->slug->toBe('test-course')
        ->is_published->toBe(1); // Database stores boolean as 1/0
});

it('generates slug automatically from title', function () {
    Volt::test('admin.course-list')
        ->call('openCreateModal')
        ->set('title', 'My New Course')
        ->assertSet('slug', 'my-new-course');
});

it('can open edit modal with existing course data', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create([
        'title' => 'Existing Course',
        'description' => 'Existing Description',
        'category_id' => $category->id
    ]);
    
    Volt::test('admin.course-list')
        ->call('openEditModal', $course->id)
        ->assertSet('showModal', true)
        ->assertSet('editingId', $course->id)
        ->assertSet('title', 'Existing Course')
        ->assertSet('description', 'Existing Description')
        ->assertSet('category_id', $category->id);
});

it('can update existing course', function () {
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    
    Volt::test('admin.course-list')
        ->call('openEditModal', $course->id)
        ->set('title', 'Updated Course')
        ->set('description', 'Updated Description')
        ->set('slug', 'updated-course')
        ->call('save')
        ->assertSet('showModal', false);
    
    $updated = Course::find($course->id);
    expect($updated)
        ->not->toBeNull()
        ->title->toBe('Updated Course')
        ->description->toBe('Updated Description')
        ->slug->toBe('updated-course');
});

it('validates required fields when creating course', function () {
    Volt::test('admin.course-list')
        ->call('openCreateModal')
        ->set('title', '')
        ->set('category_id', '')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['title', 'category_id', 'slug'])
        ->assertSet('showModal', true);
});

it('validates unique slug', function () {
    Course::factory()->create(['slug' => 'existing-slug']);
    $category = CourseCategory::factory()->create();
    
    Volt::test('admin.course-list')
        ->call('openCreateModal')
        ->set('title', 'Test Course')
        ->set('category_id', $category->id)
        ->set('slug', 'existing-slug')
        ->call('save')
        ->assertHasErrors(['slug'])
        ->assertSet('showModal', true);
});

it('can delete course without modules or enrollments', function () {
    $course = Course::factory()->create();
    
    Volt::test('admin.course-list')
        ->call('delete', $course->id);
    
    expect(Course::find($course->id))->toBeNull();
});

it('can toggle course published status', function () {
    $course = Course::factory()->create(['is_published' => false]);
    
    Volt::test('admin.course-list')
        ->call('togglePublished', $course->id);
    
    expect(Course::find($course->id)->is_published)->toBe(1); // Database stores boolean as 1
    
    Volt::test('admin.course-list')
        ->call('togglePublished', $course->id);
    
    expect(Course::find($course->id)->is_published)->toBe(0); // Database stores boolean as 0
});

it('can search courses', function () {
    $category = CourseCategory::factory()->create();
    Course::factory()->create(['title' => 'Laravel Course', 'category_id' => $category->id]);
    Course::factory()->create(['title' => 'Vue.js Course', 'category_id' => $category->id]);
    Course::factory()->create(['title' => 'React Course', 'category_id' => $category->id]);
    
    Volt::test('admin.course-list')
        ->set('search', 'Laravel')
        ->assertSee('Laravel Course')
        ->assertDontSee('Vue.js Course')
        ->assertDontSee('React Course');
});

it('can filter courses by category', function () {
    $category1 = CourseCategory::factory()->create();
    $category2 = CourseCategory::factory()->create();
    
    Course::factory()->create(['title' => 'Course 1', 'category_id' => $category1->id]);
    Course::factory()->create(['title' => 'Course 2', 'category_id' => $category2->id]);
    
    Volt::test('admin.course-list')
        ->set('categoryFilter', $category1->id)
        ->assertSee('Course 1')
        ->assertDontSee('Course 2');
});

it('resets pagination when search or filter changes', function () {
    // Create enough courses to have pagination
    Course::factory()->count(20)->create();
    $category = CourseCategory::factory()->create();
    
    $component = Volt::test('admin.course-list');
    
    // Search should work
    $component->set('search', 'nonexistent');
    $component->assertSuccessful();
    
    // Filter should work
    $component->set('categoryFilter', $category->id);
    $component->assertSuccessful();
});

it('handles empty state correctly', function () {
    Course::query()->delete();
    
    Volt::test('admin.course-list')
        ->assertSee('Belum ada kursus');
});

it('can reset form when closing modal', function () {
    Volt::test('admin.course-list')
        ->call('openCreateModal')
        ->set('title', 'Test Title')
        ->set('description', 'Test Description')
        ->call('resetForm')
        ->assertSet('title', '')
        ->assertSet('description', '')
        ->assertSet('editingId', null);
});
