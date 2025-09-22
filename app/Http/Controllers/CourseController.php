<?php

namespace App\Http\Controllers;

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

        return redirect()->route('course.continue', ['courseRecord' => $result->id]);
    }

    public function continue(CourseRecord $courseRecord)
    {
        logger()->info('Continuing course: ' . $courseRecord->id);
        if (env('APP_ENV') === 'local') {
            logger()->debug('CourseRecord details' . $courseRecord->load([
                'user',
                'course',
                'courseRecordSessions',
                'moduleRecords.lessonRecords',
            ])->toJson());
        }
        Gate::authorize('view', $courseRecord);

        return view('pages.courses.continue', compact('courseRecord'));
    }

    public function continue_lesson(CourseRecord $courseRecord, Lesson $lesson)
    {
        logger()->info('Continuing lesson: ' . $lesson->title . ' in course: ' . $courseRecord->slug);

        return view('pages.courses.continue_lesson', compact('courseRecord', 'lesson'));
    }
}
