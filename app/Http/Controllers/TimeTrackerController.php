<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TimeTrackerProject;

class TimeTrackerController extends Controller
{
    public function index(): View
    {
        return view('time-tracker.index');
    }

    public function show(TimeTrackerProject $project): View
    {
        // Authorization handled by route policy
        return view('time-tracker.show', [
            'project' => $project
        ]);
    }
}
