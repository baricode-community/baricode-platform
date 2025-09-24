<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Enrollment\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Services\CourseService;
use App\Http\Requests\CourseStartRequest;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        logger()->info('CourseController initialized');

        $this->courseService = $courseService;
        logger()->info('CourseService injected');
    }
    
    public function prepare(Course $course)
    {
        logger()->info('CourseController prepare method called with course ID: ' . $course->id);
        return view('pages.courses.prepare', compact('course'));
    }

    public function start(Course $course, CourseStartRequest $request)
    {
        logger()->info('CourseController start method called with course ID: ' . $course->id);

        $result = $this->courseService->startCourse($course, $request->validated());

        if (!$result) {
            return redirect()->back();
        }

        return redirect()->route('course.continue', ['courseEnrollment' => $result->id]);
    }

    public function continue(Enrollment $enrollment)
    {
        logger()->info('Continuing course: ' . $enrollment->id);
        Gate::authorize('view', $enrollment);

        // Load the necessary relationships to avoid N+1 queries and null reference errors
        $enrollment->load([
            'course.courseCategory',
            'courseRecordSessions',
            'moduleProgresses.courseModule',
            'moduleProgresses.lessonProgresses.lessonDetail'
        ]);

        return view('pages.courses.continue', compact('enrollment'));
    }

    public function continue_lesson(Enrollment $enrollment, LessonProgress $lessonProgress)
    {
        logger()->info('Continuing lesson: ' . $lessonProgress->id . ' in course: ' . $enrollment->id);

        return view('pages.courses.continue_lesson', compact('courseEnrollment', 'lessonProgress'));
    }
}
