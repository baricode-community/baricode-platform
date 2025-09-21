<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $courseRecords = auth()->user()->courseRecords()->where(['is_approved' => false])->get();
        return view('pages.dashboard', compact('courseRecords'));
    }

    public function profile()
    {
        $courses = collect();
        return view('pages.profile', compact('courses'));
    }
}
