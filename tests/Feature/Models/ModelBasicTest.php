<?php

use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;

describe('Model Factories and Basic Tests', function () {
    describe('User Model', function () {
        it('can create user with factory', function () {
            $user = User::factory()->create();
            
            expect($user)->toBeInstanceOf(User::class);
            expect($user->id)->not->toBeNull();
            expect($user->name)->not->toBeNull();
            expect($user->email)->not->toBeNull();
        });

        it('can create unverified user', function () {
            $user = User::factory()->unverified()->create();
            
            expect($user->email_verified_at)->toBeNull();
        });

        it('calculates initials correctly', function () {
            $user = User::factory()->create(['name' => 'John Doe']);
            expect($user->initials())->toBe('JD');

            $user2 = User::factory()->create(['name' => 'Alice']);
            expect($user2->initials())->toBe('A');
        });

        it('has correct relationships', function () {
            $user = User::factory()->create();
            
            expect($user->courseEnrollments())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
            expect($user->studentNotes())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
            expect($user->courseAttendances())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        });
    });

    describe('Course Model', function () {
        it('can create course with factory', function () {
            $course = Course::factory()->create();
            
            expect($course)->toBeInstanceOf(Course::class);
            expect($course->id)->not->toBeNull();
            expect($course->title)->not->toBeNull();
            expect($course->slug)->not->toBeNull();
        });

        it('has correct relationships', function () {
            $course = Course::factory()->create();
            
            expect($course->courseCategory())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
            expect($course->courseModules())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
            expect($course->courseEnrollments())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
            expect($course->courseAttendances())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        });

        it('can filter by published status', function () {
            Course::factory()->create(['is_published' => true]);
            Course::factory()->create(['is_published' => false]);
            
            $publishedCourses = Course::where('is_published', true)->get();
            expect($publishedCourses)->toHaveCount(1);
        });
    });

    describe('CourseCategory Model', function () {
        it('can create course category with factory', function () {
            $category = CourseCategory::factory()->create();
            
            expect($category)->toBeInstanceOf(CourseCategory::class);
            expect($category->id)->not->toBeNull();
            expect($category->name)->not->toBeNull();
        });

        it('can filter by level', function () {
            CourseCategory::factory()->create(['level' => 'pemula']);
            CourseCategory::factory()->create(['level' => 'menengah']);
            CourseCategory::factory()->create(['level' => 'pemula']);
            
            $pemulaCategories = CourseCategory::where('level', 'pemula')->get();
            expect($pemulaCategories)->toHaveCount(2);
        });
    });

    describe('CourseEnrollment Model', function () {
        it('can create course enrollment with factory', function () {
            $enrollment = CourseEnrollment::factory()->create();
            
            expect($enrollment)->toBeInstanceOf(CourseEnrollment::class);
            expect($enrollment->id)->not->toBeNull();
            expect($enrollment->user_id)->not->toBeNull();
            expect($enrollment->course_id)->not->toBeNull();
        });

        it('has correct relationships', function () {
            $enrollment = CourseEnrollment::factory()->create();
            
            expect($enrollment->user())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
            expect($enrollment->course())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        });

        it('can filter by approval status', function () {
            CourseEnrollment::factory()->create(['is_approved' => true]);
            CourseEnrollment::factory()->create(['is_approved' => false]);
            CourseEnrollment::factory()->create(['is_approved' => true]);
            
            $approvedEnrollments = CourseEnrollment::where('is_approved', true)->get();
            $pendingEnrollments = CourseEnrollment::where('is_approved', false)->get();
            
            expect($approvedEnrollments)->toHaveCount(2);
            expect($pendingEnrollments)->toHaveCount(1);
        });
    });

    describe('Model Relationships Integration', function () {
        it('user can have course enrollments', function () {
            $user = User::factory()->create();
            $course = Course::factory()->create();
            
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            
            expect($user->courseEnrollments)->toHaveCount(1);
            expect($user->courseEnrollments->first()->id)->toBe($enrollment->id);
        });

        it('course can have enrollments', function () {
            $course = Course::factory()->create();
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            CourseEnrollment::factory()->create(['course_id' => $course->id, 'user_id' => $user1->id]);
            CourseEnrollment::factory()->create(['course_id' => $course->id, 'user_id' => $user2->id]);
            
            expect($course->courseEnrollments)->toHaveCount(2);
        });

        it('course belongs to category', function () {
            $category = CourseCategory::factory()->create();
            $course = Course::factory()->create(['category_id' => $category->id]);
            
            expect($course->courseCategory->id)->toBe($category->id);
        });

        it('enrollment belongs to user and course', function () {
            $user = User::factory()->create();
            $course = Course::factory()->create();
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            
            expect($enrollment->user->id)->toBe($user->id);
            expect($enrollment->course->id)->toBe($course->id);
        });
    });

    describe('Business Logic', function () {
        it('user initials work with various name formats', function () {
            $tests = [
                ['John Doe', 'JD'],
                ['Alice', 'A'],
                ['John Michael Smith', 'JM'],
                ['', ''],
                ['A B C D', 'AB'],
            ];
            
            foreach ($tests as [$name, $expected]) {
                $user = User::factory()->create(['name' => $name]);
                expect($user->initials())->toBe($expected);
            }
        });

        it('course published status works correctly', function () {
            $publishedCourse = Course::factory()->create(['is_published' => true]);
            $unpublishedCourse = Course::factory()->create(['is_published' => false]);
            
            expect($publishedCourse->is_published)->toBe(true);
            expect($unpublishedCourse->is_published)->toBe(false);
        });

        it('enrollment approval workflow', function () {
            $enrollment = CourseEnrollment::factory()->create(['is_approved' => false]);
            
            expect($enrollment->is_approved)->toBe(false);
            
            $enrollment->update(['is_approved' => true]);
            
            expect($enrollment->fresh()->is_approved)->toBe(true);
        });
    });
});
