<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\EnrollmentLesson;
use App\Models\Enrollment\EnrollmentSession;
use App\Models\Enrollment\LessonProgress;
use App\Traits\CourseTrait;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Services\CourseService;
use App\Http\Requests\CourseStartRequest;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    use CourseTrait;

    public function __construct()
    {
        logger()->info('CourseController initialized');
    }
    
    public function prepare(Course $course)
    {
        logger()->info('CourseController prepare method called with course ID: ' . $course->id);
        return view('pages.courses.prepare', compact('course'));
    }

    public function start(Course $course, CourseStartRequest $request)
    {
        logger()->info('CourseController start method called with course ID: ' . $course->id);

        $result = $this->startCourse($course, $request->validated());

        if (!$result) {
            return redirect()->back();
        }

        return redirect()->route('course.continue', ['enrollment' => $result->id]);
    }

    public function continue(Enrollment $enrollment)
    {
        logger()->info('Continuing course: ' . $enrollment->id);
        return view('pages.courses.continue', compact('enrollment'));
    }

    public function continue_lesson(EnrollmentLesson $enrollmentLesson)
    {
        $enrollment = $enrollmentLesson->enrollmentModule->enrollment;
        logger()->info('Continuing lesson: ' . $enrollmentLesson->id . ' in course: ' . $enrollment->id);
        // dd($enrollmentLesson->lesson);

        return view('pages.courses.continue_lesson', compact('enrollment', 'enrollmentLesson'));
    }
}
