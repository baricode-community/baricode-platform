<?php

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseModule;
use App\Models\CourseEnrollment;
use App\Models\CourseAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Course Model', function () {
    describe('Model Configuration', function () {
        test('uses correct table', function () {
            $course = Course::factory()->create();
            expect($course->getTable())->toBe('courses');
        });

        test('uses HasFactory trait', function () {
            expect(Course::factory())->toBeInstanceOf(\Illuminate\Database\Eloquent\Factories\Factory::class);
        });

        test('has correct guarded attributes', function () {
            $course = Course::factory()->create();
            $guarded = $course->getGuarded();
            expect($guarded)->toContain('id');
        });

        test('can create course with all fillable attributes', function () {
            $course = Course::create([
                'title' => 'Test Course',
                'slug' => 'test-course',
                'description' => 'Test Description',
                'category_id' => CourseCategory::factory()->create()->id,
                'is_published' => true,
                'price' => 100000,
                'difficulty_level' => 'beginner',
                'duration_hours' => 10,
                'thumbnail' => 'test-thumbnail.jpg',
            ]);

            expect($course->title)->toBe('Test Course');
            expect($course->slug)->toBe('test-course');
            expect($course->description)->toBe('Test Description');
            expect($course->is_published)->toBe(true);
            expect($course->price)->toBe(100000);
            expect($course->difficulty_level)->toBe('beginner');
            expect($course->duration_hours)->toBe(10);
            expect($course->thumbnail)->toBe('test-thumbnail.jpg');
        });
    });

    describe('Factory', function () {
        test('can create course with factory', function () {
            $course = Course::factory()->create();
            
            expect($course)->toBeInstanceOf(Course::class);
            expect($course->title)->not->toBeNull();
            expect($course->slug)->not->toBeNull();
            expect($course->category_id)->not->toBeNull();
        });

        test('factory creates course with published status', function () {
            $course = Course::factory()->create(['is_published' => true]);
            expect($course->is_published)->toBe(true);
        });

        test('factory creates course with unpublished status', function () {
            $course = Course::factory()->create(['is_published' => false]);
            expect($course->is_published)->toBe(false);
        });

        test('factory can create course with specific category', function () {
            $category = CourseCategory::factory()->create();
            $course = Course::factory()->create(['category_id' => $category->id]);
            
            expect($course->category_id)->toBe($category->id);
            expect($course->courseCategory->id)->toBe($category->id);
        });
    });

    describe('Relationships', function () {
        test('belongs to course category', function () {
            $category = CourseCategory::factory()->create();
            $course = Course::factory()->create(['category_id' => $category->id]);

            expect($course->courseCategory)->toBeInstanceOf(CourseCategory::class);
            expect($course->courseCategory->id)->toBe($category->id);
        });

        test('has many course modules ordered by order column', function () {
            $course = Course::factory()->create();
            
            $module3 = CourseModule::factory()->create([
                'course_id' => $course->id,
                'order' => 3
            ]);
            $module1 = CourseModule::factory()->create([
                'course_id' => $course->id,
                'order' => 1
            ]);
            $module2 = CourseModule::factory()->create([
                'course_id' => $course->id,
                'order' => 2
            ]);

            $modules = $course->courseModules;
            
            expect($modules)->toHaveCount(3);
            expect($modules->first()->id)->toBe($module1->id);
            expect($modules->get(1)->id)->toBe($module2->id);
            expect($modules->last()->id)->toBe($module3->id);
        });

        test('has many course enrollments', function () {
            $course = Course::factory()->create();
            $enrollment1 = CourseEnrollment::factory()->create(['course_id' => $course->id]);
            $enrollment2 = CourseEnrollment::factory()->create(['course_id' => $course->id]);

            expect($course->courseEnrollments)->toHaveCount(2);
            expect($course->courseEnrollments->pluck('id'))->toContain($enrollment1->id, $enrollment2->id);
        });

        test('has many course attendances', function () {
            $course = Course::factory()->create();
            $attendance1 = CourseAttendance::factory()->create(['course_id' => $course->id]);
            $attendance2 = CourseAttendance::factory()->create(['course_id' => $course->id]);

            expect($course->courseAttendances)->toHaveCount(2);
            expect($course->courseAttendances->pluck('id'))->toContain($attendance1->id, $attendance2->id);
        });

        test('course category relationship returns null when no category', function () {
            $course = Course::factory()->create(['category_id' => null]);
            expect($course->courseCategory)->toBeNull();
        });

        test('course modules relationship returns empty collection when no modules', function () {
            $course = Course::factory()->create();
            expect($course->courseModules)->toHaveCount(0);
            expect($course->courseModules)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
        });

        test('course enrollments relationship returns empty collection when no enrollments', function () {
            $course = Course::factory()->create();
            expect($course->courseEnrollments)->toHaveCount(0);
            expect($course->courseEnrollments)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
        });

        test('course attendances relationship returns empty collection when no attendances', function () {
            $course = Course::factory()->create();
            expect($course->courseAttendances)->toHaveCount(0);
            expect($course->courseAttendances)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
        });
    });

    describe('Course Business Logic', function () {
        test('can determine if course is published', function () {
            $publishedCourse = Course::factory()->create(['is_published' => true]);
            $unpublishedCourse = Course::factory()->create(['is_published' => false]);

            expect($publishedCourse->is_published)->toBe(true);
            expect($unpublishedCourse->is_published)->toBe(false);
        });

        test('course slug is unique', function () {
            Course::factory()->create(['slug' => 'unique-course']);
            
            expect(fn() => Course::create([
                'title' => 'Another Course',
                'slug' => 'unique-course',
                'category_id' => CourseCategory::factory()->create()->id,
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('course can have price', function () {
            $freeCourse = Course::factory()->create(['price' => 0]);
            $paidCourse = Course::factory()->create(['price' => 150000]);

            expect($freeCourse->price)->toBe(0);
            expect($paidCourse->price)->toBe(150000);
        });

        test('course can have difficulty levels', function () {
            $beginnerCourse = Course::factory()->create(['difficulty_level' => 'beginner']);
            $intermediateCourse = Course::factory()->create(['difficulty_level' => 'intermediate']);
            $advancedCourse = Course::factory()->create(['difficulty_level' => 'advanced']);

            expect($beginnerCourse->difficulty_level)->toBe('beginner');
            expect($intermediateCourse->difficulty_level)->toBe('intermediate');
            expect($advancedCourse->difficulty_level)->toBe('advanced');
        });

        test('course can have duration in hours', function () {
            $course = Course::factory()->create(['duration_hours' => 20]);
            expect($course->duration_hours)->toBe(20);
        });
    });

    describe('Course Attributes', function () {
        test('title is required', function () {
            expect(fn() => Course::create([
                'slug' => 'test-course',
                'category_id' => CourseCategory::factory()->create()->id,
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('slug is required', function () {
            expect(fn() => Course::create([
                'title' => 'Test Course',
                'category_id' => CourseCategory::factory()->create()->id,
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('can have optional description', function () {
            $course = Course::factory()->create(['description' => null]);
            expect($course->description)->toBeNull();

            $courseWithDescription = Course::factory()->create(['description' => 'Test description']);
            expect($courseWithDescription->description)->toBe('Test description');
        });

        test('can have optional thumbnail', function () {
            $course = Course::factory()->create(['thumbnail' => null]);
            expect($course->thumbnail)->toBeNull();

            $courseWithThumbnail = Course::factory()->create(['thumbnail' => 'image.jpg']);
            expect($courseWithThumbnail->thumbnail)->toBe('image.jpg');
        });
    });

    describe('Mass Assignment Protection', function () {
        test('id is guarded from mass assignment', function () {
            $course = Course::create([
                'id' => 999,
                'title' => 'Test Course',
                'slug' => 'test-course',
                'category_id' => CourseCategory::factory()->create()->id,
            ]);

            expect($course->id)->not->toBe(999);
        });

        test('timestamps are automatically managed', function () {
            $course = Course::factory()->create();
            
            expect($course->created_at)->not->toBeNull();
            expect($course->updated_at)->not->toBeNull();
            expect($course->created_at)->toBeInstanceOf(\Carbon\Carbon::class);
            expect($course->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
        });
    });

    describe('Cascade Relationships', function () {
        test('deleting course does not delete category', function () {
            $category = CourseCategory::factory()->create();
            $course = Course::factory()->create(['category_id' => $category->id]);
            
            $course->delete();
            
            expect(CourseCategory::find($category->id))->not->toBeNull();
        });

        test('deleting course should handle related enrollments appropriately', function () {
            $course = Course::factory()->create();
            $enrollment = CourseEnrollment::factory()->create(['course_id' => $course->id]);
            
            // Test depends on how cascade is configured in database
            // This test structure allows for verification of the behavior
            $courseId = $course->id;
            $enrollmentId = $enrollment->id;
            
            $course->delete();
            
            // Add specific assertions based on your cascade configuration
            expect(Course::find($courseId))->toBeNull();
        });
    });

    describe('Course Query Scopes (if any)', function () {
        test('can query published courses', function () {
            Course::factory()->create(['is_published' => true]);
            Course::factory()->create(['is_published' => false]);
            
            $publishedCourses = Course::where('is_published', true)->get();
            expect($publishedCourses)->toHaveCount(1);
            expect($publishedCourses->first()->is_published)->toBe(true);
        });

        test('can query courses by category', function () {
            $category1 = CourseCategory::factory()->create();
            $category2 = CourseCategory::factory()->create();
            
            Course::factory()->create(['category_id' => $category1->id]);
            Course::factory()->create(['category_id' => $category1->id]);
            Course::factory()->create(['category_id' => $category2->id]);
            
            $category1Courses = Course::where('category_id', $category1->id)->get();
            expect($category1Courses)->toHaveCount(2);
        });

        test('can query courses by difficulty level', function () {
            Course::factory()->create(['difficulty_level' => 'beginner']);
            Course::factory()->create(['difficulty_level' => 'beginner']);
            Course::factory()->create(['difficulty_level' => 'advanced']);
            
            $beginnerCourses = Course::where('difficulty_level', 'beginner')->get();
            expect($beginnerCourses)->toHaveCount(2);
        });
    });
});
