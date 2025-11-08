<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $courseRecords = auth()->user()->courseEnrollments()->where(['is_approved' => false])->get();
        $meetRecords = auth()->user()->meets;
        $pollingRecords = auth()->user()->polls;

        return view('pages.dashboard', compact('courseRecords', 'meetRecords', 'pollingRecords'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('pages.profile', compact('user'));
    }
}
