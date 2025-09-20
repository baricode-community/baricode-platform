<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Services\CourseService;
use App\Http\Requests\CourseStartRequest;
use App\Models\Lesson;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }
    
    public function prepare(Course $course)
    {
        logger()->info('Preparing course: ' . $course->slug);
        return view('pages.courses.prepare', compact('course'));
    }

    public function start(Course $course, CourseStartRequest $request)
    {
        logger()->info('Starting course: ' . $course->slug);

        $userId = auth()->id();
        $result = $this->courseService->startCourse($course, $userId);

        if (!$result) {
            return redirect()->back();
        }

        return redirect()->route('course.continue', ['course' => $course->slug]);
    }

    public function continue(Course $course)
    {
        logger()->info('Continuing course: ' . $course->slug);

        return view('pages.courses.continue', compact('course'));
    }

    public function continue_lesson(Course $course, Lesson $lesson)
    {
        logger()->info('Continuing lesson: ' . $lesson->title . ' in course: ' . $course->slug);

        return view('pages.courses.continue_lesson', compact('course', 'lesson'));
    }

    public function continue_lesson_markAsLearned(Course $course, Lesson $lesson)
    {
        logger()->info('Marking lesson as learned: ' . $lesson->title . ' in course: ' . $course->slug);

        $userId = auth()->id();
        $this->courseService->markLessonAsLearned($lesson, $userId);

        // Redirect back to the lesson page or to the next lesson
        return redirect()->route('course.continue.lesson', ['course' => $course->slug, 'lesson' => $lesson]);
    }
}
