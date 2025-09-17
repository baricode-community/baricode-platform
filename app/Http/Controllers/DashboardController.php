<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }

    public function profile()
    {
        $courses = collect();
        return view('pages.profile', compact('courses'));
    }
}
