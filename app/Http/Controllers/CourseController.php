<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\LessonDetail;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Services\CourseService;
use App\Http\Requests\CourseStartRequest;
use App\Models\Lesson;
use App\Models\CourseRecord;
use Illuminate\Support\Facades\Gate;

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

        $result = $this->courseService->startCourse($course, $request->validated());

        if (!$result) {
            return redirect()->back();
        }

        return redirect()->route('course.continue', ['courseEnrollment' => $result->id]);
    }

    public function continue(CourseEnrollment $courseEnrollment)
    {
        logger()->info('Continuing course: ' . $courseEnrollment->id);
        Gate::authorize('view', $courseEnrollment);

        return view('pages.courses.continue', compact('courseEnrollment'));
    }

    public function continue_lesson(CourseEnrollment $courseEnrollment, LessonDetail $lesson)
    {
        logger()->info('Continuing lesson: ' . $lesson->title . ' in course: ' . $courseEnrollment->slug);

        return view('pages.courses.continue_lesson', compact('courseEnrollment', 'lesson'));
    }
}
