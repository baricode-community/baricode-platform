<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy("created_at","desc")->paginate(10);
        return view('pages.tasks.index', compact('tasks'));
    }
}
