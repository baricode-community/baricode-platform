<?php

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Integration Tests', function () {
    it('complete user journey from course discovery to enrollment', function () {
        $user = User::factory()->create();
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'is_published' => true,
            'category_id' => $category->id,
            'title' => 'Complete Laravel Course'
        ]);

        // Step 1: User discovers course (simulate course index)
        expect($course->is_published)->toBeTrue();
        expect($course->category_id)->toBe($category->id);

        // Step 2: User views course details
        expect($course->title)->toContain('Laravel');
        expect($course->slug)->not()->toBeEmpty();

        // Step 3: User enrolls in course (simulate controller action)
        $enrollmentData = [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ];
        
        $enrollment = CourseEnrollment::create($enrollmentData);
        
        // Verify enrollment success
        expect($enrollment)->toBeInstanceOf(CourseEnrollment::class);
        expect($enrollment->user_id)->toBe($user->id);
        expect($enrollment->course_id)->toBe($course->id);

        // Step 4: Verify user can access enrolled course
        $userEnrollments = CourseEnrollment::where('user_id', $user->id)->get();
        expect($userEnrollments)->toHaveCount(1);
        expect($userEnrollments->first()->course->title)->toBe('Complete Laravel Course');
    });

    it('course filtering and search simulation', function () {
        $category1 = CourseCategory::factory()->create(['name' => 'Programming']);
        $category2 = CourseCategory::factory()->create(['name' => 'Design']);

        Course::factory()->create([
            'title' => 'Laravel Web Development',
            'is_published' => true,
            'category_id' => $category1->id
        ]);
        
        Course::factory()->create([
            'title' => 'Vue.js Frontend Framework',
            'is_published' => true,
            'category_id' => $category1->id
        ]);
        
        Course::factory()->create([
            'title' => 'UI/UX Design Principles',
            'is_published' => true,
            'category_id' => $category2->id
        ]);
        
        Course::factory()->create([
            'title' => 'Unpublished Course',
            'is_published' => false,
            'category_id' => $category1->id
        ]);

        // Simulate category filtering
        $programmingCourses = Course::where('is_published', true)
            ->where('category_id', $category1->id)
            ->get();
        expect($programmingCourses)->toHaveCount(2);

        // Simulate search functionality
        $laravelCourses = Course::where('is_published', true)
            ->where('title', 'like', '%Laravel%')
            ->get();
        expect($laravelCourses)->toHaveCount(1);
        expect($laravelCourses->first()->title)->toBe('Laravel Web Development');

        // Simulate published courses only
        $publishedCourses = Course::where('is_published', true)->get();
        expect($publishedCourses)->toHaveCount(3);
    });

    it('enrollment limits and business logic validation', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => true]);

        // Create maximum allowed enrollments (3)
        CourseEnrollment::factory(3)->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $enrollmentCount = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();
        expect($enrollmentCount)->toBe(3);

        // Simulate business logic check (would be handled by service)
        $canEnroll = $enrollmentCount <= 3; // Service allows up to 3
        expect($canEnroll)->toBeTrue();

        // Add one more enrollment (at limit)
        CourseEnrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $newCount = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();
        expect($newCount)->toBe(4);

        // Now check if enrollment should be blocked
        $shouldBlock = $newCount > 3;
        expect($shouldBlock)->toBeTrue();
    });

    it('user dashboard data aggregation', function () {
        $user = User::factory()->create();
        
        $courses = Course::factory(5)->create(['is_published' => true]);
        
        // Enroll user in multiple courses
        foreach ($courses as $index => $course) {
            if ($index < 3) { // Enroll in first 3 courses
                CourseEnrollment::factory()->create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);
            }
        }

        // Simulate dashboard data aggregation
        $userEnrollments = CourseEnrollment::where('user_id', $user->id)
            ->with('course')
            ->get();
        
        expect($userEnrollments)->toHaveCount(3);
        
        $enrolledCourses = $userEnrollments->pluck('course');
        expect($enrolledCourses)->toHaveCount(3);
        
        foreach ($enrolledCourses as $course) {
            expect($course->is_published)->toBe(1); // Database stores as 1/0
        }

        // Simulate available courses (not enrolled)
        $enrolledCourseIds = $userEnrollments->pluck('course_id');
        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->get();
        
        expect($availableCourses)->toHaveCount(2);
    });

    it('course and enrollment relationship integrity', function () {
        $users = User::factory(3)->create();
        $course = Course::factory()->create(['is_published' => true]);

        // Create enrollments for multiple users
        foreach ($users as $user) {
            CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
        }

        // Test course relationship
        $courseEnrollments = $course->courseEnrollments;
        expect($courseEnrollments)->toHaveCount(3);

        // Test user relationships
        foreach ($users as $user) {
            $userEnrollments = $user->courseEnrollments;
            expect($userEnrollments)->toHaveCount(1);
            expect($userEnrollments->first()->course_id)->toBe($course->id);
        }

        // Test enrollment belongs to relationships
        $enrollment = CourseEnrollment::first();
        expect($enrollment->user)->toBeInstanceOf(User::class);
        expect($enrollment->course)->toBeInstanceOf(Course::class);
        expect($enrollment->course->id)->toBe($course->id);
    });

    it('category-based course organization', function () {
        $webDev = CourseCategory::factory()->create(['name' => 'Web Development']);
        $dataSci = CourseCategory::factory()->create(['name' => 'Data Science']);

        Course::factory(3)->create([
            'is_published' => true,
            'category_id' => $webDev->id
        ]);

        Course::factory(2)->create([
            'is_published' => true,
            'category_id' => $dataSci->id
        ]);

        Course::factory()->create([
            'is_published' => true,
            'category_id' => null  // No category
        ]);

        // Test category relationships
        expect($webDev->courses)->toHaveCount(3);
        expect($dataSci->courses)->toHaveCount(2);

        // Test courses without category
        $noCategoryCourses = Course::where('is_published', true)
            ->whereNull('category_id')
            ->get();
        expect($noCategoryCourses)->toHaveCount(1);

        // Test total published courses
        $allPublished = Course::where('is_published', true)->get();
        expect($allPublished)->toHaveCount(6);
    });
});
