<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use App\Models\Poll;

class PollingController extends Controller
{
    public function index()
    {
        return view('polls.index');
    }

    public function edit(Poll $poll)
    {
        Gate::authorize('edit', $poll);
        return view('polls.edit', compact('poll'));
    }

    public function show(Poll $poll)
    {
        return view('polls.show', compact('poll'));
    }
}
