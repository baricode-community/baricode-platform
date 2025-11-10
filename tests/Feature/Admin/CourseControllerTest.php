<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseModule;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTestCase::class);

beforeEach(function () {
    // Create admin role if it doesn't exist
    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }
    
    // Create test category
    $this->category = CourseCategory::factory()->create();
    
    Storage::fake('public');
});

describe('CourseController Index Method', function () {
    it('can display courses index with pagination', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Create courses
        Course::factory()->count(15)->create(['category_id' => $this->category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.index'));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.courses.index')
                 ->assertViewHas('courses')
                 ->assertViewHas('categories');
        
        $courses = $response->viewData('courses');
        expect($courses)->toHaveCount(10); // First page of pagination
        expect($courses->total())->toBe(15);
    });
    
    it('can filter courses by category', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $category1 = CourseCategory::factory()->create(['name' => 'Category 1']);
        $category2 = CourseCategory::factory()->create(['name' => 'Category 2']);
        
        Course::factory()->count(3)->create(['category_id' => $category1->id]);
        Course::factory()->count(2)->create(['category_id' => $category2->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.index', ['category' => $category1->id]));
        
        $response->assertStatus(200);
        
        $courses = $response->viewData('courses');
        expect($courses)->toHaveCount(3);
        
        $selectedCategory = $response->viewData('selectedCategory');
        expect($selectedCategory->id)->toBe($category1->id);
    });
    
    it('loads courses with category relationship', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        Course::factory()->create([
            'title' => 'Test Course',
            'category_id' => $this->category->id
        ]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.index'));
        
        $response->assertStatus(200);
        
        $courses = $response->viewData('courses');
        $course = $courses->first();
        expect($course->courseCategory)->not->toBeNull();
        expect($course->courseCategory->id)->toBe($this->category->id);
    });
});

describe('CourseController Create Method', function () {
    it('can display create form with categories', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Create active and inactive categories
        $activeCategory = CourseCategory::factory()->create();
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.create'));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.courses.create')
                 ->assertViewHas('categories');
        
        $categories = $response->viewData('categories');
        expect($categories->contains($activeCategory))->toBeTrue();
    });
    
    it('can pre-select category from query parameter', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.create', ['category' => $this->category->id]));
        
        $response->assertStatus(200);
        
        $selectedCategoryId = $response->viewData('selectedCategoryId');
        expect($selectedCategoryId)->toBe($this->category->id);
    });
});

describe('CourseController Store Method', function () {
    it('can create course with valid data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $courseData = [
            'title' => 'New Test Course',
            'description' => 'Test course description',
            'category_id' => $this->category->id,
            'price' => 100000,
            'duration_hours' => 40,
            'level' => 'intermediate'
        ];
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.courses.store'), $courseData);
        
        $response->assertRedirect();
        
        expect(Course::where('title', 'New Test Course')->exists())->toBeTrue();
        
        $course = Course::where('title', 'New Test Course')->first();
        expect($course->category_id)->toBe($this->category->id);
        expect($course->price)->toBe(100000);
        expect($course->duration_hours)->toBe(40);
        expect($course->level)->toBe('intermediate');
    });
    
    it('can create course with thumbnail upload', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg', 800, 600);
        
        $courseData = [
            'title' => 'Course with Thumbnail',
            'category_id' => $this->category->id,
            'level' => 'beginner',
            'thumbnail' => $thumbnail
        ];
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.courses.store'), $courseData);
        
        $response->assertRedirect();
        
        $course = Course::where('title', 'Course with Thumbnail')->first();
        expect($course)->not->toBeNull();
        expect($course->thumbnail)->not->toBeNull();
        
        Storage::disk('public')->assertExists($course->thumbnail);
    });
    
    it('validates required fields', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.courses.store'), []);
        
        $response->assertSessionHasErrors(['title', 'category_id', 'level']);
    });
    
    it('validates level field values', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $validLevels = ['beginner', 'intermediate', 'advanced'];
        
        foreach ($validLevels as $level) {
            $response = $this->actingAs($adminUser)
                             ->post(route('admin.courses.store'), [
                                 'title' => "Course $level",
                                 'category_id' => $this->category->id,
                                 'level' => $level
                             ]);
            
            $response->assertRedirect();
            
            $course = Course::where('title', "Course $level")->first();
            expect($course->level)->toBe($level);
        }
    });
    
    it('validates category exists', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.courses.store'), [
                             'title' => 'Test Course',
                             'category_id' => 999999,
                             'level' => 'beginner'
                         ]);
        
        $response->assertSessionHasErrors(['category_id']);
    });
    
    it('validates thumbnail file type and size', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        
        $response = $this->actingAs($adminUser)
                         ->post(route('admin.courses.store'), [
                             'title' => 'Test Course',
                             'category_id' => $this->category->id,
                             'level' => 'beginner',
                             'thumbnail' => $invalidFile
                         ]);
        
        $response->assertSessionHasErrors(['thumbnail']);
    });
});

describe('CourseController Show Method', function () {
    it('can display course details with relationships', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        CourseModule::factory()->count(3)->create(['course_id' => $course->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.show', $course));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.courses.show')
                 ->assertViewHas('course');
        
        $viewCourse = $response->viewData('course');
        expect($viewCourse->courseCategory)->not->toBeNull();
        expect($viewCourse->courseModules)->toHaveCount(3);
    });
});

describe('CourseController Edit Method', function () {
    it('can display edit form with existing data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create([
            'title' => 'Edit Test Course',
            'description' => 'Original description',
            'category_id' => $this->category->id
        ]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.edit', $course));
        
        $response->assertStatus(200)
                 ->assertViewIs('admin.courses.edit')
                 ->assertViewHas('course')
                 ->assertViewHas('categories')
                 ->assertSee('Edit Test Course')
                 ->assertSee('Original description');
    });
});

describe('CourseController Update Method', function () {
    it('can update course with valid data', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create([
            'title' => 'Original Title',
            'category_id' => $this->category->id,
            'level' => 'beginner'
        ]);
        
        $newCategory = CourseCategory::factory()->create();
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'category_id' => $newCategory->id,
            'level' => 'advanced',
            'price' => 150000,
            'duration_hours' => 50
        ];
        
        $response = $this->actingAs($adminUser)
                         ->put(route('admin.courses.update', $course), $updateData);
        
        $response->assertRedirect();
        
        $updatedCourse = Course::find($course->id);
        expect($updatedCourse->title)->toBe('Updated Title');
        expect($updatedCourse->description)->toBe('Updated description');
        expect($updatedCourse->category_id)->toBe($newCategory->id);
        expect($updatedCourse->level)->toBe('advanced');
        expect($updatedCourse->price)->toBe(150000);
        expect($updatedCourse->duration_hours)->toBe(50);
    });
    
    it('can update course thumbnail', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Create course with existing thumbnail
        $oldThumbnail = UploadedFile::fake()->image('old.jpg');
        Storage::disk('public')->put('old-thumbnail.jpg', $oldThumbnail);
        
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail' => 'old-thumbnail.jpg'
        ]);
        
        // Update with new thumbnail
        $newThumbnail = UploadedFile::fake()->image('new.jpg');
        
        $response = $this->actingAs($adminUser)
                         ->put(route('admin.courses.update', $course), [
                             'title' => $course->title,
                             'category_id' => $course->category_id,
                             'level' => 'beginner',
                             'thumbnail' => $newThumbnail
                         ]);
        
        $response->assertRedirect();
        
        $updatedCourse = Course::find($course->id);
        expect($updatedCourse->thumbnail)->not->toBe('old-thumbnail.jpg');
        
        Storage::disk('public')->assertExists($updatedCourse->thumbnail);
    });
});

describe('CourseController Destroy Method', function () {
    it('can delete course without modules', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        $courseId = $course->id;
        
        $response = $this->actingAs($adminUser)
                         ->delete(route('admin.courses.destroy', $course));
        
        $response->assertRedirect();
        
        expect(Course::find($courseId))->toBeNull();
    });
    
    it('prevents deletion of course with modules', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        CourseModule::factory()->create(['course_id' => $course->id]);
        
        $response = $this->actingAs($adminUser)
                         ->delete(route('admin.courses.destroy', $course));
        
        $response->assertRedirect();
        
        expect(Course::find($course->id))->not->toBeNull();
    });
    
    it('deletes thumbnail when deleting course', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');
        Storage::disk('public')->put('test-thumbnail.jpg', $thumbnail);
        
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'thumbnail' => 'test-thumbnail.jpg'
        ]);
        
        $response = $this->actingAs($adminUser)
                         ->delete(route('admin.courses.destroy', $course));
        
        $response->assertRedirect();
        
        Storage::disk('public')->assertMissing('test-thumbnail.jpg');
    });
});

describe('CourseController Modules Method', function () {
    it('redirects to course modules index', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.modules', $course));
        
        $response->assertRedirect(route('admin.course-modules.index', ['course' => $course->id]));
    });
});

describe('CourseController Access Control', function () {
    it('requires authentication for all routes', function () {
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        
        $routes = [
            ['get', route('admin.courses.index')],
            ['get', route('admin.courses.create')],
            ['post', route('admin.courses.store')],
            ['get', route('admin.courses.show', $course)],
            ['get', route('admin.courses.edit', $course)],
            ['put', route('admin.courses.update', $course)],
            ['delete', route('admin.courses.destroy', $course)],
        ];
        
        foreach ($routes as [$method, $url]) {
            $response = $this->$method($url);
            $response->assertRedirect(route('login'));
        }
    });
    
    it('requires admin role for all routes', function () {
        $regularUser = User::factory()->create();
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        
        $routes = [
            ['get', route('admin.courses.index')],
            ['get', route('admin.courses.create')],
            ['post', route('admin.courses.store')],
            ['get', route('admin.courses.show', $course)],
            ['get', route('admin.courses.edit', $course)],
            ['put', route('admin.courses.update', $course)],
            ['delete', route('admin.courses.destroy', $course)],
        ];
        
        foreach ($routes as [$method, $url]) {
            $response = $this->actingAs($regularUser)->$method($url);
            $response->assertStatus(403);
        }
    });
});

describe('CourseController Edge Cases', function () {
    it('handles empty courses list', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.index'));
        
        $response->assertStatus(200);
        
        $courses = $response->viewData('courses');
        expect($courses)->toHaveCount(0);
    });
    
    it('handles course with many modules', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        CourseModule::factory()->count(50)->create(['course_id' => $course->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.show', $course));
        
        $response->assertStatus(200);
        
        $viewCourse = $response->viewData('course');
        expect($viewCourse->courseModules)->toHaveCount(50);
    });
    
    it('handles non-existent category filter gracefully', function () {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        Course::factory()->count(3)->create(['category_id' => $this->category->id]);
        
        $response = $this->actingAs($adminUser)
                         ->get(route('admin.courses.index', ['category' => 999999]));
        
        $response->assertStatus(200);
        
        $courses = $response->viewData('courses');
        expect($courses)->toHaveCount(0);
        
        $selectedCategory = $response->viewData('selectedCategory');
        expect($selectedCategory)->toBeNull();
    });
});
