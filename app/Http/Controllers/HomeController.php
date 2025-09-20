<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home.index');
    }

    public function about()
    {
        return view('pages.home.about');
    }

    public function cara_belajar()
    {
        return view('pages.home.cara_belajar');
    }

    public function courses()
    {
        $categories = \App\Models\Category::with([
            'courses' => function ($q) {
                $q->where('is_published', true);
            },
        ])->get();

        return view('pages.home.course.index', compact('categories'));
    }

    public function course(Course $course)
    {
        return view('pages.home.course.show', compact('course'));
    }
}
