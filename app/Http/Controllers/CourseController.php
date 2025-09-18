<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Services\CourseService;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function start(Course $course)
    {
        $userId = auth()->id();
        $result = $this->courseService->startCourse($course, $userId);

        if (!$result) {
            return redirect()->back();
        }

        return redirect()->route('course.continue', ['course' => $course->slug]);
    }

    public function continue(Course $course)
    {
        return view('pages.courses.continue', compact('course'));
    }
}
