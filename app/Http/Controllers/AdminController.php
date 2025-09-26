<?php

namespace App\Http\Controllers;

use App\Models\Course\Course;
use App\Models\User\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users_count = User::where('email_verified_at', '!=', null)->count();
        $courses_count = Course::where('is_published', true)->count();
        return view('pages.admin.index', compact('courses_count', 'users_count'));
    }
}
