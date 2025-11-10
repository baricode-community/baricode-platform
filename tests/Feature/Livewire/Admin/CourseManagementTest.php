<?php

use Tests\DatabaseTestCase;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Course;
use App\Models\Learning\CourseModule;
use App\Models\Learning\CourseModuleLesson;
use Livewire\Volt\Volt;

uses(DatabaseTestCase::class);

it('can render course management dashboard', function () {
    Volt::test('admin.course-management')
        ->assertSuccessful()
        ->assertSee('Manajemen Kursus')
        ->assertSee('Kelola semua kategori, kursus, modul, dan pelajaran');
});

it('displays correct stats on dashboard', function () {
    // Create test data
    $categories = CourseCategory::factory()->count(5)->create();
    $courses = Course::factory()->count(10)->create();
    $modules = CourseModule::factory()->count(15)->create();
    $lessons = CourseModuleLesson::factory()->count(25)->create();
    
    Volt::test('admin.course-management')
        ->assertSuccessful()
        ->assertSee('5') // Categories count
        ->assertSee('10') // Courses count
        ->assertSee('15') // Modules count
        ->assertSee('25'); // Lessons count
});

it('can switch between tabs', function () {
    Volt::test('admin.course-management')
        ->assertSet('activeTab', 'categories')
        ->call('setActiveTab', 'courses')
        ->assertSet('activeTab', 'courses')
        ->call('setActiveTab', 'modules')
        ->assertSet('activeTab', 'modules')
        ->call('setActiveTab', 'lessons')
        ->assertSet('activeTab', 'lessons');
});

it('shows appropriate content based on active tab', function () {
    Volt::test('admin.course-management')
        ->set('activeTab', 'categories')
        ->assertSee('Kategori'); // This should be visible in the tab navigation
});

it('has working navigation between tabs', function () {
    Volt::test('admin.course-management')
        ->assertSee('Kategori')
        ->assertSee('Kursus')
        ->assertSee('Modul')
        ->assertSee('Pelajaran');
});

it('loads stats correctly on mount', function () {
    // Clear any existing data
    CourseCategory::truncate();
    Course::truncate();
    CourseModule::truncate();
    CourseModuleLesson::truncate();
    
    // Create known test data
    CourseCategory::factory()->count(3)->create();
    Course::factory()->count(7)->create();
    CourseModule::factory()->count(12)->create();
    CourseModuleLesson::factory()->count(20)->create();
    
    $component = Volt::test('admin.course-management');
    
    // Check that stats are loaded and match expected ranges
    expect($component->get('categoriesCount'))->toBe(3);
    expect($component->get('coursesCount'))->toBeGreaterThanOrEqual(7); // May be higher due to factory relationships
    expect($component->get('modulesCount'))->toBeGreaterThanOrEqual(12);
    expect($component->get('lessonsCount'))->toBeGreaterThanOrEqual(20);
});

it('refreshes stats when loadStats is called', function () {
    $component = Volt::test('admin.course-management');
    
    // Initial state
    expect($component->get('categoriesCount'))->toBe(0);
    
    // Add data
    CourseCategory::factory()->create();
    
    // Refresh stats
    $component->call('loadStats');
    
    expect($component->get('categoriesCount'))->toBe(1);
});
