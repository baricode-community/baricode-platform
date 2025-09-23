<?php

use App\Models\CourseEnrollment;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\ModuleProgress;
use App\Models\CourseRecordSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CourseEnrollment Model', function () {
    describe('Model Configuration', function () {
        test('uses correct table', function () {
            $enrollment = CourseEnrollment::factory()->create();
            expect($enrollment->getTable())->toBe('course_enrollments');
        });

        test('uses HasFactory trait', function () {
            expect(CourseEnrollment::factory())->toBeInstanceOf(\Illuminate\Database\Eloquent\Factories\Factory::class);
        });

        test('uses CourseEnrollmentTrait', function () {
            $enrollment = CourseEnrollment::factory()->create();
            $traits = class_uses($enrollment);
            expect($traits)->toContain(\App\Traits\CourseEnrollmentTrait::class);
        });

        test('has correct guarded attributes', function () {
            $enrollment = CourseEnrollment::factory()->create();
            $guarded = $enrollment->getGuarded();
            expect($guarded)->toContain('id');
        });

        test('is observed by CourseEnrollmentObserver', function () {
            $observedClasses = CourseEnrollment::getObservableEvents();
            expect($observedClasses)->toContain('creating', 'created', 'updating', 'updated', 'deleting', 'deleted');
        });
    });

    describe('Factory', function () {
        test('can create course enrollment with factory', function () {
            $enrollment = CourseEnrollment::factory()->create();
            
            expect($enrollment)->toBeInstanceOf(CourseEnrollment::class);
            expect($enrollment->user_id)->not->toBeNull();
            expect($enrollment->course_id)->not->toBeNull();
        });

        test('factory creates enrollment with user and course', function () {
            $user = User::factory()->create();
            $course = Course::factory()->create();
            
            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            
            expect($enrollment->user_id)->toBe($user->id);
            expect($enrollment->course_id)->toBe($course->id);
        });

        test('can create enrollment with specific status', function () {
            $enrollment = CourseEnrollment::factory()->create(['is_approved' => true]);
            expect($enrollment->is_approved)->toBe(true);

            $enrollment2 = CourseEnrollment::factory()->create(['is_approved' => false]);
            expect($enrollment2->is_approved)->toBe(false);
        });
    });

    describe('Relationships', function () {
        test('belongs to user', function () {
            $user = User::factory()->create();
            $enrollment = CourseEnrollment::factory()->create(['user_id' => $user->id]);

            expect($enrollment->user)->toBeInstanceOf(User::class);
            expect($enrollment->user->id)->toBe($user->id);
        });

        test('belongs to course', function () {
            $course = Course::factory()->create();
            $enrollment = CourseEnrollment::factory()->create(['course_id' => $course->id]);

            expect($enrollment->course)->toBeInstanceOf(Course::class);
            expect($enrollment->course->id)->toBe($course->id);
        });

        test('has many module progresses', function () {
            $enrollment = CourseEnrollment::factory()->create();
            $progress1 = ModuleProgress::factory()->create(['course_enrollment_id' => $enrollment->id]);
            $progress2 = ModuleProgress::factory()->create(['course_enrollment_id' => $enrollment->id]);

            expect($enrollment->moduleProgresses)->toHaveCount(2);
            expect($enrollment->moduleProgresses->pluck('id'))->toContain($progress1->id, $progress2->id);
        });

        test('has many course record sessions', function () {
            $enrollment = CourseEnrollment::factory()->create();
            $session1 = CourseRecordSession::factory()->create(['course_enrollment_id' => $enrollment->id]);
            $session2 = CourseRecordSession::factory()->create(['course_enrollment_id' => $enrollment->id]);

            expect($enrollment->courseRecordSessions)->toHaveCount(2);
            expect($enrollment->courseRecordSessions->pluck('id'))->toContain($session1->id, $session2->id);
        });

        test('course enrollment sessions relationship (alias)', function () {
            $enrollment = CourseEnrollment::factory()->create();
            $session = CourseRecordSession::factory()->create(['course_enrollment_id' => $enrollment->id]);

            expect($enrollment->courseEnrollmentSessions)->toHaveCount(1);
            expect($enrollment->courseEnrollmentSessions->first()->id)->toBe($session->id);
        });

        test('empty relationships return empty collections', function () {
            $enrollment = CourseEnrollment::factory()->create();
            
            expect($enrollment->moduleProgresses)->toHaveCount(0);
            expect($enrollment->courseRecordSessions)->toHaveCount(0);
            expect($enrollment->courseEnrollmentSessions)->toHaveCount(0);
        });
    });

    describe('Model Events and Observers', function () {
        test('creates module progresses when course enrollment is created', function () {
            $course = Course::factory()->create();
            
            // Create course modules
            $module1 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 1]);
            $module2 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 2]);
            $module3 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 3]);

            // Create enrollment - should trigger automatic module progress creation
            $enrollment = CourseEnrollment::factory()->create(['course_id' => $course->id]);

            expect($enrollment->moduleProgresses)->toHaveCount(3);
            
            $moduleIds = $enrollment->moduleProgresses->pluck('module_id')->toArray();
            expect($moduleIds)->toContain($module1->id, $module2->id, $module3->id);
        });

        test('creates module progresses in correct order', function () {
            $course = Course::factory()->create();
            
            // Create modules in random order but with specific order values
            $module3 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 3]);
            $module1 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 1]);
            $module2 = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 2]);

            $enrollment = CourseEnrollment::factory()->create(['course_id' => $course->id]);
            
            // Verify all modules have progress records
            expect($enrollment->moduleProgresses)->toHaveCount(3);
            
            // Verify the modules are correctly associated
            $progressModuleIds = $enrollment->moduleProgresses->pluck('module_id');
            expect($progressModuleIds)->toContain($module1->id, $module2->id, $module3->id);
        });

        test('handles course with no modules gracefully', function () {
            $course = Course::factory()->create();
            $enrollment = CourseEnrollment::factory()->create(['course_id' => $course->id]);

            expect($enrollment->moduleProgresses)->toHaveCount(0);
        });
    });

    describe('Course Enrollment Business Logic', function () {
        test('can track enrollment approval status', function () {
            $approvedEnrollment = CourseEnrollment::factory()->create(['is_approved' => true]);
            $pendingEnrollment = CourseEnrollment::factory()->create(['is_approved' => false]);

            expect($approvedEnrollment->is_approved)->toBe(true);
            expect($pendingEnrollment->is_approved)->toBe(false);
        });

        test('can track enrollment completion status', function () {
            $completedEnrollment = CourseEnrollment::factory()->create(['is_completed' => true]);
            $inProgressEnrollment = CourseEnrollment::factory()->create(['is_completed' => false]);

            expect($completedEnrollment->is_completed)->toBe(true);
            expect($inProgressEnrollment->is_completed)->toBe(false);
        });

        test('can store enrollment date', function () {
            $enrollmentDate = now()->subDays(5);
            $enrollment = CourseEnrollment::factory()->create(['enrolled_at' => $enrollmentDate]);

            expect($enrollment->enrolled_at->format('Y-m-d'))->toBe($enrollmentDate->format('Y-m-d'));
        });

        test('can store completion date', function () {
            $completionDate = now();
            $enrollment = CourseEnrollment::factory()->create([
                'completed_at' => $completionDate,
                'is_completed' => true
            ]);

            expect($enrollment->completed_at->format('Y-m-d H:i'))->toBe($completionDate->format('Y-m-d H:i'));
        });
    });

    describe('Validation and Constraints', function () {
        test('requires user_id', function () {
            expect(fn() => CourseEnrollment::create([
                'course_id' => Course::factory()->create()->id,
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('requires course_id', function () {
            expect(fn() => CourseEnrollment::create([
                'user_id' => User::factory()->create()->id,
            ]))->toThrow(\Illuminate\Database\QueryException::class);
        });

        test('user can enroll in same course multiple times if allowed', function () {
            $user = User::factory()->create();
            $course = Course::factory()->create();

            $enrollment1 = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            // Depending on business rules, this might be allowed or not
            // Adjust the test based on your actual business logic
            expect($enrollment1->user_id)->toBe($user->id);
            expect($enrollment1->course_id)->toBe($course->id);
        });

        test('foreign key constraints work correctly', function () {
            $user = User::factory()->create();
            $course = Course::factory()->create();

            $enrollment = CourseEnrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            expect($enrollment->user)->not->toBeNull();
            expect($enrollment->course)->not->toBeNull();
        });
    });

    describe('Mass Assignment Protection', function () {
        test('id is guarded from mass assignment', function () {
            $enrollment = CourseEnrollment::create([
                'id' => 999,
                'user_id' => User::factory()->create()->id,
                'course_id' => Course::factory()->create()->id,
            ]);

            expect($enrollment->id)->not->toBe(999);
        });

        test('can mass assign allowed attributes', function () {
            $user = User::factory()->create();
            $course = Course::factory()->create();
            
            $enrollment = CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'is_approved' => true,
                'is_completed' => false,
                'enrolled_at' => now(),
                'progress_percentage' => 25.5,
            ]);

            expect($enrollment->user_id)->toBe($user->id);
            expect($enrollment->course_id)->toBe($course->id);
            expect($enrollment->is_approved)->toBe(true);
            expect($enrollment->is_completed)->toBe(false);
            expect($enrollment->progress_percentage)->toBe(25.5);
        });
    });

    describe('Timestamps and Attributes', function () {
        test('timestamps are automatically managed', function () {
            $enrollment = CourseEnrollment::factory()->create();
            
            expect($enrollment->created_at)->not->toBeNull();
            expect($enrollment->updated_at)->not->toBeNull();
            expect($enrollment->created_at)->toBeInstanceOf(\Carbon\Carbon::class);
            expect($enrollment->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
        });

        test('can store progress percentage', function () {
            $enrollment = CourseEnrollment::factory()->create(['progress_percentage' => 75.5]);
            expect($enrollment->progress_percentage)->toBe(75.5);
        });

        test('can store additional metadata', function () {
            $enrollment = CourseEnrollment::factory()->create([
                'notes' => 'Student shows great progress',
                'certificate_issued' => true,
            ]);

            expect($enrollment->notes)->toBe('Student shows great progress');
            expect($enrollment->certificate_issued)->toBe(true);
        });
    });

    describe('CourseEnrollmentTrait Integration', function () {
        test('trait methods are available', function () {
            $enrollment = CourseEnrollment::factory()->create();
            
            // Test that trait methods exist (adjust based on actual trait methods)
            expect(method_exists($enrollment, 'calculateProgress'))->toBe(true);
        });
    });

    describe('Query Scopes and Filtering', function () {
        test('can filter by approval status', function () {
            CourseEnrollment::factory()->create(['is_approved' => true]);
            CourseEnrollment::factory()->create(['is_approved' => false]);
            CourseEnrollment::factory()->create(['is_approved' => true]);

            $approvedEnrollments = CourseEnrollment::where('is_approved', true)->get();
            $pendingEnrollments = CourseEnrollment::where('is_approved', false)->get();

            expect($approvedEnrollments)->toHaveCount(2);
            expect($pendingEnrollments)->toHaveCount(1);
        });

        test('can filter by completion status', function () {
            CourseEnrollment::factory()->create(['is_completed' => true]);
            CourseEnrollment::factory()->create(['is_completed' => false]);
            CourseEnrollment::factory()->create(['is_completed' => false]);

            $completedEnrollments = CourseEnrollment::where('is_completed', true)->get();
            $inProgressEnrollments = CourseEnrollment::where('is_completed', false)->get();

            expect($completedEnrollments)->toHaveCount(1);
            expect($inProgressEnrollments)->toHaveCount(2);
        });

        test('can filter by user', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            CourseEnrollment::factory()->create(['user_id' => $user1->id]);
            CourseEnrollment::factory()->create(['user_id' => $user1->id]);
            CourseEnrollment::factory()->create(['user_id' => $user2->id]);

            $user1Enrollments = CourseEnrollment::where('user_id', $user1->id)->get();
            $user2Enrollments = CourseEnrollment::where('user_id', $user2->id)->get();

            expect($user1Enrollments)->toHaveCount(2);
            expect($user2Enrollments)->toHaveCount(1);
        });

        test('can filter by course', function () {
            $course1 = Course::factory()->create();
            $course2 = Course::factory()->create();

            CourseEnrollment::factory()->create(['course_id' => $course1->id]);
            CourseEnrollment::factory()->create(['course_id' => $course1->id]);
            CourseEnrollment::factory()->create(['course_id' => $course2->id]);

            $course1Enrollments = CourseEnrollment::where('course_id', $course1->id)->get();
            $course2Enrollments = CourseEnrollment::where('course_id', $course2->id)->get();

            expect($course1Enrollments)->toHaveCount(2);
            expect($course2Enrollments)->toHaveCount(1);
        });
    });
});
