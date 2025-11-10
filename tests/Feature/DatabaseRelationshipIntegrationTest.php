<?php

use Tests\DatabaseTestCase;
use App\Models\User\User;
use App\Models\User\UserNote;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Course;
use App\Models\Learning\CourseModule;
use App\Models\Learning\CourseModuleLesson;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\EnrollmentModule;
use App\Models\Enrollment\EnrollmentLesson;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

uses(DatabaseTestCase::class);

it('creates complete course structure and enrollments', function () {
    // Create course category
    $category = CourseCategory::factory()->create([
        'name' => 'Web Development',
        'level' => 'menengah'
    ]);

    // Create course
    $course = Course::factory()->create([
        'title' => 'Full Stack PHP Development',
        'category_id' => $category->id,
        'is_published' => true
    ]);

    // Create modules
    $module1 = CourseModule::factory()->create([
        'course_id' => $course->id,
        'name' => 'PHP Basics',
        'order' => 1
    ]);
    
    $module2 = CourseModule::factory()->create([
        'course_id' => $course->id,
        'name' => 'Laravel Framework',
        'order' => 2
    ]);

    // Create lessons
    $lesson1 = CourseModuleLesson::factory()->create([
        'module_id' => $module1->id,
        'title' => 'Variables and Data Types',
        'order' => 1
    ]);
    
    $lesson2 = CourseModuleLesson::factory()->create([
        'module_id' => $module1->id,
        'title' => 'Control Structures',
        'order' => 2
    ]);
    
    $lesson3 = CourseModuleLesson::factory()->create([
        'module_id' => $module2->id,
        'title' => 'Routing and Controllers',
        'order' => 1
    ]);

    // Create users
    $student1 = User::factory()->create(['name' => 'John Doe', 'level' => 'menengah']);
    $student2 = User::factory()->create(['name' => 'Jane Smith', 'level' => 'menengah']);

    // Create enrollments
    $enrollment1 = Enrollment::factory()->create([
        'user_id' => $student1->id,
        'course_id' => $course->id,
        'is_approved' => true
    ]);
    
    $enrollment2 = Enrollment::factory()->create([
        'user_id' => $student2->id,
        'course_id' => $course->id,
        'is_approved' => false
    ]);

    // Verify course structure
    expect($category->courses)->toHaveCount(1);
    expect($course->courseModules)->toHaveCount(2);
    expect(CourseModuleLesson::count())->toBe(3);
    
    // Verify course modules are ordered correctly
    $orderedModules = $course->courseModules()->orderBy('order')->get();
    expect($orderedModules[0]->name)->toBe('PHP Basics');
    expect($orderedModules[1]->name)->toBe('Laravel Framework');
    
    // Verify lessons are ordered correctly within modules
    $module1Lessons = $module1->courseModuleLessons()->orderBy('order')->get();
    expect($module1Lessons)->toHaveCount(2);
    expect($module1Lessons[0]->title)->toBe('Variables and Data Types');
    expect($module1Lessons[1]->title)->toBe('Control Structures');

    // Verify enrollments
    expect($course->enrollments)->toHaveCount(2);
    expect($student1->courseEnrollments)->toHaveCount(1);
    expect($student2->courseEnrollments)->toHaveCount(1);
    
    // Note: Since auto-creation of enrollment modules is disabled in testing,
    // we won't test that here
});

it('handles complex enrollment progression workflow', function () {
    // Setup course structure
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    $lessons = CourseModuleLesson::factory()->count(3)->create(['module_id' => $module->id]);
    
    $user = User::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id
    ]);

    // Create enrollment modules and lessons
    $enrollmentModule = EnrollmentModule::factory()->create([
        'enrollment_id' => $enrollment->id,
        'module_id' => $module->id
    ]);
    
    $enrollmentLessons = collect();
    foreach ($lessons as $lesson) {
        $enrollmentLessons->push(EnrollmentLesson::factory()->create([
            'enrollment_module_id' => $enrollmentModule->id,
            'lesson_id' => $lesson->id
        ]));
    }

    // Test progression through lessons
    foreach ($enrollmentLessons as $index => $enrollmentLesson) {
        expect($enrollmentLesson->is_completed)->toBeFalse();
        
        // Complete the lesson
        $enrollmentLesson->update(['is_completed' => true]);
        $enrollmentLesson->refresh();
        
        expect($enrollmentLesson->is_completed)->toBeTrue();
    }

    // Mark module as completed
    $enrollmentModule->update(['is_completed' => true]);
    expect($enrollmentModule->is_completed)->toBeTrue();

    // Verify all relationships are working
    expect($enrollmentModule->enrollment->user->id)->toBe($user->id);
    expect($enrollmentModule->enrollment->course->id)->toBe($course->id);
    expect($enrollmentModule->module_id)->toBe($module->id);
    expect($enrollmentModule->enrollmentLessons)->toHaveCount(3);
});

it('handles user notes across multiple courses', function () {
    // Create multiple courses with different structures
    $categories = CourseCategory::factory()->count(2)->create();
    $courses = collect();
    $allLessons = collect();

    foreach ($categories as $category) {
        $course = Course::factory()->create(['category_id' => $category->id]);
        $courses->push($course);
        
        $modules = CourseModule::factory()->count(2)->create(['course_id' => $course->id]);
        foreach ($modules as $module) {
            $lessons = CourseModuleLesson::factory()->count(2)->create(['module_id' => $module->id]);
            $allLessons = $allLessons->concat($lessons);
        }
    }

    $user = User::factory()->create();
    
    // Create notes for various lessons across different courses
    $notes = collect();
    foreach ($allLessons as $lesson) {
        $notes->push(UserNote::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id
        ]));
    }

    // Verify user has notes for all lessons
    expect($user->userNotes)->toHaveCount($allLessons->count());
    
    // Verify each lesson can have user notes
    foreach ($allLessons as $lesson) {
        expect($lesson->userNotes)->toHaveCount(1);
    }

    // Test accessing course information through user notes
    $firstNote = $notes->first();
    $courseFromNote = CourseModuleLesson::with('courseModule.course')
                                      ->find($firstNote->lesson_id)
                                      ->courseModule
                                      ->course;
    
    expect($courses->pluck('id'))->toContain($courseFromNote->id);
});

it('handles cascading deletes properly', function () {
    // Create complete course structure
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $module = CourseModule::factory()->create(['course_id' => $course->id]);
    $lesson = CourseModuleLesson::factory()->create(['module_id' => $module->id]);
    
    $user = User::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id
    ]);
    
    $enrollmentModule = EnrollmentModule::factory()->create([
        'enrollment_id' => $enrollment->id,
        'module_id' => $module->id
    ]);
    
    $enrollmentLesson = EnrollmentLesson::factory()->create([
        'enrollment_module_id' => $enrollmentModule->id,
        'lesson_id' => $lesson->id
    ]);
    
    $userNote = UserNote::factory()->create([
        'user_id' => $user->id,
        'lesson_id' => $lesson->id
    ]);

    // Store IDs for verification
    $courseId = $course->id;
    $moduleId = $module->id;
    $lessonId = $lesson->id;
    $enrollmentId = $enrollment->id;
    $enrollmentModuleId = $enrollmentModule->id;
    $enrollmentLessonId = $enrollmentLesson->id;
    $userNoteId = $userNote->id;

    // Test cascading delete when category is deleted
    $category->delete();

    // Verify all related records are deleted
    expect(Course::find($courseId))->toBeNull();
    expect(CourseModule::find($moduleId))->toBeNull();
    expect(CourseModuleLesson::find($lessonId))->toBeNull();
    expect(Enrollment::find($enrollmentId))->toBeNull();
    expect(EnrollmentModule::find($enrollmentModuleId))->toBeNull();
    expect(EnrollmentLesson::find($enrollmentLessonId))->toBeNull();
    expect(UserNote::find($userNoteId))->toBeNull();
});

it('maintains data integrity with foreign key constraints', function () {
    // Test that foreign key constraints prevent orphaned records
    $category = CourseCategory::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    
    // Try to create course with non-existent category
    expect(fn () => Course::factory()->create(['category_id' => 99999]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
