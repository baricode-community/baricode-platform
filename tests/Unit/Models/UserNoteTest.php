<?php

use Tests\DatabaseTestCase;
use App\Models\User\UserNote;
use App\Models\User\User;
use App\Models\Learning\CourseModuleLesson;
use App\Models\Learning\CourseModule;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

uses(DatabaseTestCase::class);

beforeEach(function () {
    $category = CourseCategory::factory()->create();
    $this->course = Course::factory()->create(['category_id' => $category->id]);
    $this->module = CourseModule::factory()->create(['course_id' => $this->course->id]);
    $this->lesson = CourseModuleLesson::factory()->create(['module_id' => $this->module->id]);
    $this->user = User::factory()->create();
    $this->userNote = UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id
    ]);
});

it('has user relationship', function () {
    expect(method_exists($this->userNote, 'user'))->toBeTrue();
    expect($this->userNote->user())->toBeInstanceOf(BelongsTo::class);
});

it('belongs to a user', function () {
    expect($this->userNote->user->id)->toBe($this->user->id);
    expect($this->userNote->user->name)->toBe($this->user->name);
});

it('has proper user note attributes', function () {
    $userNoteData = [
        'title' => 'Important Concept',
        'note' => 'This lesson covers variables and their scope in PHP. Remember to use proper naming conventions.'
    ];
    
    $userNote = UserNote::factory()->create(array_merge($userNoteData, [
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id
    ]));

    expect($userNote->title)->toBe($userNoteData['title']);
    expect($userNote->note)->toBe($userNoteData['note']);
});

it('belongs to a lesson', function () {
    // UserNote should be associated with a lesson through lesson_id foreign key
    expect($this->userNote->lesson_id)->toBe($this->lesson->id);
    
    // We can verify the lesson exists by querying directly
    $this->assertDatabaseHas('course_module_lessons', [
        'id' => $this->userNote->lesson_id,
        'title' => $this->lesson->title
    ]);
});

it('can access lesson through database relationship', function () {
    // Since the model doesn't have a lesson() method defined, we can test the database relationship
    $lessonFromDb = CourseModuleLesson::find($this->userNote->lesson_id);
    
    expect($lessonFromDb->id)->toBe($this->lesson->id);
    expect($lessonFromDb->title)->toBe($this->lesson->title);
});

it('requires user_id field', function () {
    expect(fn () => UserNote::factory()->create([
        'user_id' => null,
        'lesson_id' => $this->lesson->id
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires lesson_id field', function () {
    expect(fn () => UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires title field', function () {
    expect(fn () => UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id,
        'title' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('requires note field', function () {
    expect(fn () => UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id,
        'note' => null
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('allows user to have multiple notes for same lesson', function () {
    $note1 = UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id,
        'title' => 'First Note',
        'note' => 'This is my first note for this lesson'
    ]);
    
    $note2 = UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id,
        'title' => 'Second Note',
        'note' => 'This is my second note for the same lesson'
    ]);

    $userNotesForLesson = UserNote::where('user_id', $this->user->id)
                                  ->where('lesson_id', $this->lesson->id)
                                  ->get();
    
    expect($userNotesForLesson)->toHaveCount(3); // Including the one from setUp
    expect($userNotesForLesson->contains('id', $note1->id))->toBeTrue();
    expect($userNotesForLesson->contains('id', $note2->id))->toBeTrue();
});

it('allows multiple users to have notes for same lesson', function () {
    $user2 = User::factory()->create();
    
    $note1 = UserNote::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson->id,
        'title' => 'User 1 Note'
    ]);
    
    $note2 = UserNote::factory()->create([
        'user_id' => $user2->id,
        'lesson_id' => $this->lesson->id,
        'title' => 'User 2 Note'
    ]);

    $notesForLesson = UserNote::where('lesson_id', $this->lesson->id)->get();
    
    expect($notesForLesson)->toHaveCount(3); // Including the one from setUp
    expect($notesForLesson->contains('user_id', $this->user->id))->toBeTrue();
    expect($notesForLesson->contains('user_id', $user2->id))->toBeTrue();
});

it('deletes when user is deleted', function () {
    $userNoteId = $this->userNote->id;
    
    // Delete user (should cascade to user notes)
    $this->user->delete();

    // Verify cascading delete
    $this->assertDatabaseMissing('user_notes', ['id' => $userNoteId]);
});

it('deletes when lesson is deleted', function () {
    $userNoteId = $this->userNote->id;
    
    // Delete lesson (should cascade to user notes)
    $this->lesson->delete();

    // Verify cascading delete
    $this->assertDatabaseMissing('user_notes', ['id' => $userNoteId]);
});

it('can access course through lesson relationship', function () {
    // Test accessing course through the lesson
    $lessonFromDb = CourseModuleLesson::with('courseModule.course')->find($this->userNote->lesson_id);
    $courseFromNote = $lessonFromDb->courseModule->course;
    
    expect($courseFromNote->id)->toBe($this->course->id);
    expect($courseFromNote->title)->toBe($this->course->title);
});

it('can access module through lesson relationship', function () {
    // Test accessing module through the lesson
    $lessonFromDb = CourseModuleLesson::with('courseModule')->find($this->userNote->lesson_id);
    $moduleFromNote = $lessonFromDb->courseModule;
    
    expect($moduleFromNote->id)->toBe($this->module->id);
    expect($moduleFromNote->name)->toBe($this->module->name);
});

it('has timestamps', function () {
    expect($this->userNote->created_at)->not->toBeNull();
    expect($this->userNote->updated_at)->not->toBeNull();
});
