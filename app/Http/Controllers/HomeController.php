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

    public function tos()
    {
        return view('pages.home.tos');
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
        $categories = Category::whereHas('courses', function ($q) {
            $q->where('is_published', true);
        })->get();

        return view('pages.home.course.index', compact('categories'));
    }

    public function course(Course $course)
    {
        if (! $course->is_published) {
            abort(404);
        }

        return view('pages.home.course.show', compact('course'));
    }

    public function pemula()
    {
        $categories = Category::where('level', 'pemula')->get();

        return view('pages.home.course.level.pemula', compact('categories'));
    }

    public function menengah()
    {
        $categories = Category::where('level', 'menengah')->get();

        return view('pages.home.course.level.menengah', compact('categories'));
    }

    public function lanjut()
    {
        $categories = Category::where('level', 'lanjut')->get();

        return view('pages.home.course.level.lanjut', compact('categories'));
    }
}
