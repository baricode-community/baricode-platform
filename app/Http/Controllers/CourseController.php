<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Services\CourseService;
use App\Http\Requests\CourseStartRequest;
use App\Models\Lesson;
use App\Models\CourseRecord;

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

        $result = $this->courseService->startCourse($course);

        if (!$result) {
            return redirect()->back();
        }

        return redirect()->route('course.continue', ['course' => $result->slug]);
    }

    public function continue(CourseRecord $courseRecord)
    {
        logger()->info('Continuing course: ' . $courseRecord->slug);
        
        return view('pages.courses.continue', compact('courseRecord'));
    }

    public function continue_lesson(CourseRecord $courseRecord, Lesson $lesson)
    {
        logger()->info('Continuing lesson: ' . $lesson->title . ' in course: ' . $courseRecord->slug);

        return view('pages.courses.continue_lesson', compact('courseRecord', 'lesson'));
    }

    public function continue_lesson_markAsLearned(CourseRecord $courseRecord, Lesson $lesson)
    {
        logger()->info('Marking lesson as learned: ' . $lesson->title . ' in course: ' . $courseRecord->slug);

        $userId = auth()->id();
        $this->courseService->markLessonAsLearned($lesson, $userId);

        // Redirect back to the lesson page or to the next lesson
        return redirect()->route('course.continue.lesson', ['course' => $courseRecord->slug, 'lesson' => $lesson]);
    }
}
