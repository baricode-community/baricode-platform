<?php

use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseModule;
use App\Models\ModuleProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

describe('Observer Tests', function () {
    describe('UserObserver', function () {
        it('logs user creation', function () {
            $user = User::factory()->make();
            
            Log::shouldReceive('info')
                ->once()
                ->with('User created: ' . $user->email);

            $user->save();
        });

        it('logs user updates', function () {
            $user = User::factory()->create();
            
            Log::shouldReceive('info')
                ->once()
                ->with('User updated: ' . $user->email);

            $user->update(['name' => 'Updated Name']);
        });

        it('logs user deletion', function () {
            $user = User::factory()->create();
            
            Log::shouldReceive('info')
                ->once()
                ->with('User deleted: ' . $user->email);

            $user->delete();
        });
    });

    describe('CourseEnrollmentObserver', function () {
        it('logs course enrollment creation', function () {
            $course = Course::factory()->create();
            $user = User::factory()->create();
            $enrollment = CourseEnrollment::factory()->make([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            Log::shouldReceive('info')
                ->once()
                ->with('CourseEnrollment created', \Mockery::on(function ($arg) {
                    return is_array($arg) && isset($arg['id']) && is_numeric($arg['id']);
                }));
            
            // In local environment, also expects debug log
            if (env('APP_ENV') === 'local') {
                Log::shouldReceive('debug')
                    ->once()
                    ->with(\Mockery::pattern('/CourseEnrollment details/'));
            }

            $enrollment->save();
        });

        it('logs course enrollment updates', function () {
            $enrollment = CourseEnrollment::factory()->create();

            Log::shouldReceive('info')
                ->once()
                ->with('CourseEnrollment updated', ['id' => $enrollment->id]);

            // Change a field that will trigger the updated event
            $enrollment->is_approved = !$enrollment->is_approved;
            $enrollment->save();
        });

        it('logs course enrollment deletion', function () {
            $enrollment = CourseEnrollment::factory()->create();

            Log::shouldReceive('info')
                ->once()
                ->with('CourseEnrollment deleted', ['id' => $enrollment->id]);

            $enrollment->delete();
        });
    });

    describe('CourseEnrollment Model Events', function () {
        it('creates module progresses when course enrollment is created', function () {
            $course = Course::factory()->create();
            $user = User::factory()->create();
            
            // Create modules for the course with unique order
            $module1 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 1]);
            $module2 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 2]);
            $module3 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 3]);

            // Disable observer logging for this test
            Log::shouldReceive('info')->andReturn();
            if (env('APP_ENV') === 'local') {
                Log::shouldReceive('debug')->andReturn();
            }

            // Create enrollment
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            // Check that module progresses were created
            $moduleProgresses = ModuleProgress::where('course_enrollment_id', $enrollment->id)->get();
            
            expect($moduleProgresses)->toHaveCount(3);
            
            $moduleIds = $moduleProgresses->pluck('module_id')->toArray();
            expect($moduleIds)->toContain($module1->id)
                ->and($moduleIds)->toContain($module2->id)
                ->and($moduleIds)->toContain($module3->id);
        });

        it('does not create module progresses if course has no modules', function () {
            $course = Course::factory()->create();
            $user = User::factory()->create();

            // Disable observer logging for this test
            Log::shouldReceive('info')->andReturn();
            if (env('APP_ENV') === 'local') {
                Log::shouldReceive('debug')->andReturn();
            }

            // Create enrollment for course with no modules
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            // Check that no module progresses were created
            $moduleProgresses = ModuleProgress::where('course_enrollment_id', $enrollment->id)->get();
            
            expect($moduleProgresses)->toHaveCount(0);
        });
    });

    describe('Observer Integration', function () {
        it('user creation and course enrollment work together', function () {
            // Log expectations
            Log::shouldReceive('info')
                ->with(\Mockery::pattern('/User created:/'))
                ->once();
            
            Log::shouldReceive('info')
                ->with('CourseEnrollment created', \Mockery::on(function ($arg) {
                    return is_array($arg) && isset($arg['id']) && is_numeric($arg['id']);
                }))
                ->once();
                
            if (env('APP_ENV') === 'local') {
                Log::shouldReceive('debug')
                    ->with(\Mockery::pattern('/CourseEnrollment details/'))
                    ->once();
            }

            $course = Course::factory()->create();
            $user = User::factory()->create();

            // Create modules with unique order
            CourseModule::factory()->create(['course_id' => $course->id, 'order' => 1]);
            CourseModule::factory()->create(['course_id' => $course->id, 'order' => 2]);

            // Create enrollment
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            // Verify everything was created correctly
            expect($user->exists())->toBeTrue()
                ->and($enrollment->exists())->toBeTrue()
                ->and($enrollment->moduleProgresses)->toHaveCount(2);
        });
    });
});
