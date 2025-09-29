<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Course\CourseCategory;
use App\Models\Course\Course;

class HomeController extends Controller
{
    public function __construct()
    {
        logger()->info('HomeController initialized');
    }

    public function index()
    {
        logger()->info('HomeController index method called');
        return view('pages.home.index');
    }

    public function tos()
    {
        logger()->info('HomeController tos method called');
        return view('pages.home.tos');
    }

    public function about()
    {
        logger()->info('HomeController about method called');
        return view('pages.home.about');
    }

    public function cara_belajar()
    {
        logger()->info('HomeController cara_belajar method called');
        return view('pages.home.cara_belajar');
    }

    public function profile(User $user)
    {
        logger()->info('HomeController profile method called for user ID: ' . $user->id);
        return view('pages.home.profile', compact('user'));
    }

    public function courses()
    {
        logger()->info('HomeController courses method called');

        $categories = CourseCategory::get()->sortBy('name');
        logger()->info('Fetched ' . $categories->count() . ' course categories.');
        return view('pages.home.course.index', compact('categories'));
    }

    public function course(Course $course)
    {
        logger()->info('HomeController course method called with course ID: ' . $course->id);

        if (! $course->is_published) {
            logger()->warning('Attempted to access unpublished course ID: ' . $course->id);
            abort(404);
        }
        return view('pages.home.course.show', compact('course'));
    }

    public function pemula()
    {
        logger()->info('HomeController pemula method called');

        $categories = CourseCategory::where('level', 'pemula')->get();
        return view('pages.home.course.level.pemula', compact('categories'));
    }

    public function menengah()
    {
        logger()->info('HomeController menengah method called');

        $categories = CourseCategory::where('level', 'menengah')->get();
        return view('pages.home.course.level.menengah', compact('categories'));
    }

    public function lanjut()
    {
        logger()->info('HomeController lanjut method called');
        
        $categories = CourseCategory::where('level', 'lanjut')->get();
        return view('pages.home.course.level.lanjut', compact('categories'));
    }
}
