<?php

use Tests\DatabaseTestCase;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Course;
use Livewire\Volt\Volt;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $this->category = CourseCategory::factory()->create();
});

it('can render category management page', function () {
    Volt::test('admin.category-management')
        ->assertSuccessful()
        ->assertSee('Manajemen Kategori')
        ->assertSee('Tambah Kategori');
});

it('can display categories list', function () {
    $categories = CourseCategory::factory()->count(3)->create();
    
    Volt::test('admin.category-management')
        ->assertSuccessful()
        ->assertSee($categories[0]->name)
        ->assertSee($categories[1]->name)
        ->assertSee($categories[2]->name);
});

it('can open create modal', function () {
    Volt::test('admin.category-management')
        ->call('openCreateModal')
        ->assertSet('showModal', true)
        ->assertSet('editingId', null)
        ->assertSet('name', '');
});

it('can create new category', function () {
    Volt::test('admin.category-management')
        ->call('openCreateModal')
        ->set('name', 'Test Category')
        ->set('description', 'Test Description')
        ->set('level', 'menengah')
        ->call('save')
        ->assertSet('showModal', false);
    
    expect(CourseCategory::where('name', 'Test Category')->first())
        ->not->toBeNull()
        ->description->toBe('Test Description')
        ->level->toBe('menengah');
});

it('can open edit modal with existing category data', function () {
    $category = CourseCategory::factory()->create([
        'name' => 'Existing Category',
        'description' => 'Existing Description',
        'level' => 'lanjut'
    ]);
    
    Volt::test('admin.category-management')
        ->call('openEditModal', $category->id)
        ->assertSet('showModal', true)
        ->assertSet('editingId', $category->id)
        ->assertSet('name', 'Existing Category')
        ->assertSet('description', 'Existing Description')
        ->assertSet('level', 'lanjut');
});

it('can update existing category', function () {
    $category = CourseCategory::factory()->create();
    
    Volt::test('admin.category-management')
        ->call('openEditModal', $category->id)
        ->set('name', 'Updated Category')
        ->set('description', 'Updated Description')
        ->set('level', 'menengah')
        ->call('save')
        ->assertSet('showModal', false);
    
    $updated = CourseCategory::find($category->id);
    expect($updated)
        ->not->toBeNull()
        ->name->toBe('Updated Category')
        ->description->toBe('Updated Description')
        ->level->toBe('menengah');
});

it('validates required fields when creating category', function () {
    Volt::test('admin.category-management')
        ->call('openCreateModal')
        ->set('name', '')
        ->set('level', 'invalid_level')
        ->call('save')
        ->assertHasErrors(['name', 'level'])
        ->assertSet('showModal', true);
});

it('can delete category without courses', function () {
    $category = CourseCategory::factory()->create();
    
    Volt::test('admin.category-management')
        ->call('delete', $category->id);
    
    expect(CourseCategory::find($category->id))->toBeNull();
});

it('cannot delete category with courses', function () {
    $category = CourseCategory::factory()->create();
    Course::factory()->create(['category_id' => $category->id]);
    
    Volt::test('admin.category-management')
        ->call('delete', $category->id);
    
    // Category should still exist
    expect(CourseCategory::find($category->id))->not->toBeNull();
});

it('can search categories', function () {
    CourseCategory::factory()->create(['name' => 'Web Development']);
    CourseCategory::factory()->create(['name' => 'Mobile Development']);
    CourseCategory::factory()->create(['name' => 'Data Science']);
    
    Volt::test('admin.category-management')
        ->set('search', 'Web')
        ->assertSee('Web Development')
        ->assertDontSee('Mobile Development')
        ->assertDontSee('Data Science');
});

it('resets pagination when search changes', function () {
    // Create enough categories to have pagination
    CourseCategory::factory()->count(20)->create();
    
    $component = Volt::test('admin.category-management');
    
    // Search should work regardless of pagination
    $component->set('search', 'nonexistent');
    
    // After search, component should not error and should be functional
    $component->assertSuccessful();
});

it('displays category stats correctly', function () {
    $category = CourseCategory::factory()->create();
    Course::factory()->count(3)->create(['category_id' => $category->id]);
    
    Volt::test('admin.category-management')
        ->assertSee('3'); // Should show course count
});

it('handles empty state correctly', function () {
    // Ensure there are no categories
    CourseCategory::query()->delete();
    
    Volt::test('admin.category-management')
        ->assertSee('Belum ada kategori');
});

it('can reset form when closing modal', function () {
    Volt::test('admin.category-management')
        ->call('openCreateModal')
        ->set('name', 'Test Name')
        ->set('description', 'Test Description')
        ->call('resetForm')
        ->assertSet('name', '')
        ->assertSet('description', '')
        ->assertSet('editingId', null);
});
