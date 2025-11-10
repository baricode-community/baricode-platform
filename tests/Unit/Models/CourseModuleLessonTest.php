<?php

use Tests\DatabaseTestCase;
use App\Models\Learning\CourseModuleLesson;
use App\Models\Learning\CourseModule;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\User\UserNote;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $category = CourseCategory::factory()->create();
    $this->course = Course::factory()->create(['category_id' => $category->id]);
    $this->module = CourseModule::factory()->create(['course_id' => $this->course->id]);
    $this->lesson = CourseModuleLesson::factory()->create(['module_id' => $this->module->id]);
});

it('has course module relationship', function () {
    expect(method_exists($this->lesson, 'courseModule'))->toBeTrue();
    expect($this->lesson->courseModule())->toBeInstanceOf(BelongsTo::class);
});

it('belongs to a course module', function () {
    expect($this->lesson->courseModule->id)->toBe($this->module->id);
    expect($this->lesson->courseModule->name)->toBe($this->module->name);
});

it('has user notes relationship', function () {
    expect(method_exists($this->lesson, 'userNotes'))->toBeTrue();
    expect($this->lesson->userNotes())->toBeInstanceOf(HasMany::class);
});

it('can have many user notes', function () {
    $users = User::factory()->count(3)->create();
    $notes = collect();

    foreach ($users as $user) {
        $notes->push(UserNote::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $this->lesson->id
        ]));
    }

    $lessonNotes = $this->lesson->userNotes()->get();
    
    expect($lessonNotes)->toHaveCount(3);
    expect($lessonNotes->pluck('id')->sort()->toArray())
        ->toBe($notes->pluck('id')->sort()->toArray());
});

it('has proper lesson attributes', function () {
    $lessonData = [
        'title' => 'Variables and Data Types',
        'content' => 'In this lesson, we will learn about PHP variables and data types...',
        'order' => 1
    ];
    
    $lesson = CourseModuleLesson::factory()->create(array_merge($lessonData, ['module_id' => $this->module->id]));

    expect($lesson->title)->toBe($lessonData['title']);
    expect($lesson->content)->toBe($lessonData['content']);
    expect($lesson->order)->toBe($lessonData['order']);
});

it('can have null content', function () {
    $lesson = CourseModuleLesson::factory()->create([
        'module_id' => $this->module->id,
        'content' => null
    ]);
    
    expect($lesson->content)->toBeNull();
});

it('defaults order to negative one', function () {
    // Test with direct creation
    $lessonData = [
        'module_id' => $this->module->id,
        'title' => 'Test Lesson'
    ];
    
    $lesson = new CourseModuleLesson($lessonData);
    $lesson->save();
    
    // Check database default
    $this->assertDatabaseHas('course_module_lessons', [
        'title' => 'Test Lesson',
        'order' => -1
    ]);
});

it('can be ordered within module', function () {
    // Create lessons with different orders in the same module  
    $lesson1 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 10]);
    $lesson2 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 20]);
    $lesson3 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 30]);

    $orderedLessons = $this->module->courseModuleLessons()->orderBy('order')->get();
    
    // Find lessons by order since there might be existing lesson from setUp  
    $orderedLesson1 = $orderedLessons->where('order', 10)->first();
    $orderedLesson2 = $orderedLessons->where('order', 20)->first();  
    $orderedLesson3 = $orderedLessons->where('order', 30)->first();
    
    expect($orderedLesson1->id)->toBe($lesson1->id);
    expect($orderedLesson2->id)->toBe($lesson2->id);
    expect($orderedLesson3->id)->toBe($lesson3->id);
});

it('allows same order in different modules', function () {
    $category2 = CourseCategory::factory()->create();
    $course2 = Course::factory()->create(['category_id' => $category2->id]);
    $module2 = CourseModule::factory()->create(['course_id' => $course2->id]);

    // Same order is allowed for different modules
    $lesson1 = CourseModuleLesson::factory()->create(['module_id' => $this->module->id, 'order' => 1]);
    $lesson2 = CourseModuleLesson::factory()->create(['module_id' => $module2->id, 'order' => 1]);

    expect($lesson1->order)->toBe(1);
    expect($lesson2->order)->toBe(1);
});

it('deletes related data when lesson is deleted', function () {
    $users = User::factory()->count(2)->create();
    $notes = collect();

    foreach ($users as $user) {
        $notes->push(UserNote::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $this->lesson->id
        ]));
    }

    $lessonId = $this->lesson->id;
    
    // Delete lesson
    $this->lesson->delete();

    // Verify cascading deletes
    foreach ($notes as $note) {
        $this->assertDatabaseMissing('user_notes', ['id' => $note->id]);
    }
});

it('requires title field', function () {
    expect(fn () => CourseModuleLesson::factory()->create([
        'module_id' => $this->module->id,
        'title' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires module_id field', function () {
    expect(fn () => CourseModuleLesson::factory()->create(['module_id' => null]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('can access course through module', function () {
    // Test accessing course through the module relationship
    $courseFromLesson = $this->lesson->courseModule->course;
    
    expect($courseFromLesson->id)->toBe($this->course->id);
    expect($courseFromLesson->title)->toBe($this->course->title);
});

it('can have multiple notes from same user', function () {
    $user = User::factory()->create();
    
    $note1 = UserNote::factory()->create([
        'user_id' => $user->id,
        'lesson_id' => $this->lesson->id,
        'title' => 'First Note'
    ]);
    
    $note2 = UserNote::factory()->create([
        'user_id' => $user->id,
        'lesson_id' => $this->lesson->id,
        'title' => 'Second Note'
    ]);

    $userNotesForLesson = $this->lesson->userNotes()->where('user_id', $user->id)->get();
    
    expect($userNotesForLesson)->toHaveCount(2);
    expect($userNotesForLesson->contains('id', $note1->id))->toBeTrue();
    expect($userNotesForLesson->contains('id', $note2->id))->toBeTrue();
});
