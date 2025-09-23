<?php

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

describe('Service Layer Tests', function () {
    it('CourseService creates enrollment successfully', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => true]);
        $service = new CourseService();
        
        Auth::login($user);

        $request = [
            'days' => [
                'Monday' => ['sesi_1' => '08:00'],
                'Wednesday' => ['sesi_1' => '09:00'],
            ]
        ];

        $result = $service->startCourse($course, $request);

        expect($result)->toBeInstanceOf(CourseEnrollment::class);
        expect($result->user_id)->toBe($user->id);
        expect($result->course_id)->toBe($course->id);
        
        // Check database
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
        expect($enrollment)->not()->toBeNull();
    });

    it('CourseService returns null for unauthenticated user', function () {
        $course = Course::factory()->create(['is_published' => true]);
        $service = new CourseService();
        
        Auth::logout();

        $request = ['days' => []];
        $result = $service->startCourse($course, $request);

        expect($result)->toBeNull();
    });

    it('CourseService returns null for unpublished course', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => false]);
        $service = new CourseService();
        
        Auth::login($user);

        $request = ['days' => []];
        $result = $service->startCourse($course, $request);

        expect($result)->toBeNull();
    });

    it('CourseService prevents enrollment when limit exceeded', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => true]);
        $service = new CourseService();
        
        Auth::login($user);
        
        // Create 4 existing enrollments to exceed the limit
        CourseEnrollment::factory(4)->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $request = ['days' => ['Monday' => ['sesi_1' => '08:00']]];
        $result = $service->startCourse($course, $request);

        expect($result)->toBeNull();
        
        // Verify count unchanged
        $enrollmentCount = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();
        expect($enrollmentCount)->toBe(4);
    });

    it('CourseService handles empty request array', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => true]);
        $service = new CourseService();
        
        Auth::login($user);
        
        $emptyRequest = [];
        $result = $service->startCourse($course, $emptyRequest);

        expect($result)->toBeInstanceOf(CourseEnrollment::class);
        expect($result->user_id)->toBe($user->id);
    });

    it('CourseService validates business logic with proper enrollment count', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create(['is_published' => true]);
        $service = new CourseService();
        
        Auth::login($user);
        
        // Create 3 enrollments (exactly at limit)
        CourseEnrollment::factory(3)->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $request = ['days' => []];
        $result = $service->startCourse($course, $request);

        // Should still allow one more
        expect($result)->toBeInstanceOf(CourseEnrollment::class);
        
        $enrollmentCount = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();
        expect($enrollmentCount)->toBe(4);
    });
});
