<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeTrackerController extends Controller
{
    public function index(): View
    {
        return view('time-tracker.index');
    }
}
