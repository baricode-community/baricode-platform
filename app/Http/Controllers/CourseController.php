<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function start(Course $course)
    {
        $context = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'user_id' => auth()->id(),
        ];

        logger()->info('Starting course', $context);

        if (!$course->is_published) {
            logger()->warning('Attempt to start unpublished course', $context);
            return redirect()->back()->with('error', 'Course is not published yet.');
        }

        flash()->success('You have successfully started the course!');
        return redirect()->route('courses');
    }
}
