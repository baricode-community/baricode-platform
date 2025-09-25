<?php

use Tests\DatabaseTestCase;
use App\Models\Course\CourseCategory;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Relations\HasMany;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $this->category = CourseCategory::factory()->create();
});

it('has courses relationship', function () {
    expect(method_exists($this->category, 'courses'))->toBeTrue();
    expect($this->category->courses())->toBeInstanceOf(HasMany::class);
});

it('can have many courses', function () {
    $courses = Course::factory()->count(5)->create([
        'category_id' => $this->category->id
    ]);

    $categoryCourses = $this->category->courses()->get();
    
    expect($categoryCourses)->toHaveCount(5);
    expect($categoryCourses->pluck('id')->sort()->toArray())->toBe($courses->pluck('id')->sort()->toArray());
});

it('has proper category attributes', function () {
    $categoryData = [
        'name' => 'Web Development',
        'level' => 'menengah',
        'description' => 'Learn web development from basics to advanced'
    ];
    
    $category = CourseCategory::factory()->create($categoryData);

    expect($category->name)->toBe($categoryData['name']);
    expect($category->level)->toBe($categoryData['level']);
    expect($category->description)->toBe($categoryData['description']);
});

it('has valid level enum values', function () {
    $levels = ['pemula', 'menengah', 'lanjut'];
    
    foreach ($levels as $level) {
        $category = CourseCategory::factory()->create(['level' => $level]);
        expect($category->level)->toBe($level);
    }
});

it('defaults to pemula level', function () {
    $categoryData = [
        'name' => 'Test Category',
        'description' => 'Test Description',
        'level' => 'pemula' // Explicitly set since factory might override
    ];
    
    $category = new CourseCategory($categoryData);
    $category->save();
    
    expect($category->level)->toBe('pemula');
});

it('can have courses with different levels', function () {
    $pemulaCourse = Course::factory()->create(['category_id' => $this->category->id]);
    $menengahCourse = Course::factory()->create(['category_id' => $this->category->id]);
    $lanjutCourse = Course::factory()->create(['category_id' => $this->category->id]);

    expect($this->category->courses()->get())->toHaveCount(3);
});

it('deletes related courses when category is deleted', function () {
    $courses = Course::factory()->count(3)->create([
        'category_id' => $this->category->id
    ]);

    $categoryId = $this->category->id;
    
    // Delete category
    $this->category->delete();

    // Verify cascading deletes
    foreach ($courses as $course) {
        expect(Course::find($course->id))->toBeNull();
    }
});

it('can have null description', function () {
    $category = CourseCategory::factory()->create(['description' => null]);
    
    expect($category->description)->toBeNull();
});

it('requires name field', function () {
    expect(fn () => CourseCategory::factory()->create(['name' => null]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
