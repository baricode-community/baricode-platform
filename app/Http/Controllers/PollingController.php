<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;

class PollingController extends Controller
{
    public function index()
    {
        return view('polls.index');
    }

    public function show(Poll $poll)
    {
        return view('polls.show', compact('poll'));
    }
}
