<?php

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseModule;
use App\Models\User;
use App\Models\LessonDetail;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CourseController Basic Tests', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->course = Course::factory()->create(['is_published' => true]);
    });

    describe('prepare method', function () {
        it('shows course preparation page for authenticated user', function () {
            $response = $this->actingAs($this->user)
                ->get(route('course.prepare', $this->course));

            $response->assertStatus(200)
                ->assertViewIs('pages.courses.prepare')
                ->assertViewHas('course', $this->course);
        });

        it('requires authentication for course preparation', function () {
            $response = $this->get(route('course.prepare', $this->course));

            $response->assertRedirect(route('login'));
        });
    });

    describe('start method', function () {
        it('requires authenticated user to start course', function () {
            $validData = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => '13:00',
                        'sesi_3' => '17:00',
                    ]
                ]
            ];

            $response = $this->post(route('course.start', $this->course), $validData);

            $response->assertRedirect(route('login'));
        });

        it('validates request data format', function () {
            $invalidData = [
                'days' => [
                    '1' => [
                        'sesi_1' => 'invalid-time-format',
                        'sesi_2' => '25:00', // Invalid hour
                        'sesi_3' => '17:00',
                    ]
                ]
            ];

            $response = $this->actingAs($this->user)
                ->post(route('course.start', $this->course), $invalidData);

            $response->assertSessionHasErrors(['days.1.sesi_1', 'days.1.sesi_2']);
        });
    });

    describe('continue method', function () {
        it('shows course continuation page for enrollment owner', function () {
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $this->user->id,
                'course_id' => $this->course->id,
                'is_approved' => true,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('course.continue', $enrollment));

            $response->assertStatus(200)
                ->assertViewIs('pages.courses.continue')
                ->assertViewHas('courseEnrollment', $enrollment);
        });

        it('requires authentication for course continuation', function () {
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $this->user->id,
                'course_id' => $this->course->id,
                'is_approved' => true,
            ]);

            $response = $this->get(route('course.continue', $enrollment));

            $response->assertRedirect(route('login'));
        });
    });

    describe('continue_lesson method', function () {
        it('shows lesson continuation page for authenticated user', function () {
            $module = CourseModule::factory()->create([
                'course_id' => $this->course->id
            ]);
            
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $this->user->id,
                'course_id' => $this->course->id,
                'is_approved' => true,
            ]);

            $lesson = LessonDetail::factory()->create([
                'title' => 'Sample Lesson',
                'module_id' => $module->id,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('course.continue.lesson', [$enrollment, $lesson]));

            $response->assertStatus(200)
                ->assertViewIs('pages.courses.continue_lesson')
                ->assertViewHas('courseEnrollment', $enrollment)
                ->assertViewHas('lesson', $lesson);
        });

        it('requires authentication for lesson continuation', function () {
            $module = CourseModule::factory()->create([
                'course_id' => $this->course->id
            ]);
            
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $this->user->id,
                'course_id' => $this->course->id,
                'is_approved' => true,
            ]);

            $lesson = LessonDetail::factory()->create([
                'title' => 'Sample Lesson',
                'module_id' => $module->id,
            ]);

            $response = $this->get(route('course.continue.lesson', [$enrollment, $lesson]));

            $response->assertRedirect(route('login'));
        });
    });

    describe('Service Integration', function () {
        it('CourseService startCourse integration works', function () {
            // Test the actual service method behavior
            $service = new CourseService();
            
            // Create a published course
            $course = Course::factory()->create(['is_published' => true]);
            
            $validRequest = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => '13:00',
                        'sesi_3' => '17:00',
                    ]
                ]
            ];

            $this->actingAs($this->user);
            
            $result = $service->startCourse($course, $validRequest);
            
            expect($result)->toBeInstanceOf(CourseEnrollment::class)
                ->and($result->user_id)->toBe($this->user->id)
                ->and($result->course_id)->toBe($course->id);
        });

        it('CourseService prevents starting unpublished course', function () {
            $service = new CourseService();
            
            // Create an unpublished course
            $course = Course::factory()->create(['is_published' => false]);
            
            $validRequest = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => '13:00',
                        'sesi_3' => '17:00',
                    ]
                ]
            ];

            $this->actingAs($this->user);
            
            $result = $service->startCourse($course, $validRequest);
            
            expect($result)->toBeNull();
        });

        it('CourseService prevents starting course for unauthenticated user', function () {
            $service = new CourseService();
            
            $course = Course::factory()->create(['is_published' => true]);
            
            $validRequest = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => '13:00',
                        'sesi_3' => '17:00',
                    ]
                ]
            ];

            // No authenticated user
            
            $result = $service->startCourse($course, $validRequest);
            
            expect($result)->toBeNull();
        });
    });
});
