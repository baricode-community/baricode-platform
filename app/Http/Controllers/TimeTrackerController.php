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
        // Ensure the project belongs to the authenticated user
        if ($project->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this project.');
        }

        return view('time-tracker.show', [
            'project' => $project
        ]);
    }
}
